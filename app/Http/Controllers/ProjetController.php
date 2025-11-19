<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjetStoreRequest;
use App\Http\Requests\ProjetUpdateRequest;
use App\Mail\ProjectInvitationMail;
use App\Models\ProjectInvitation;
use App\Models\Projet;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ProjetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $scope = $request->query('scope', 'all');
        $status = $request->query('status');
        $visibility = $request->query('visibility');
        $createdFrom = $request->query('created_from');
        $createdTo = $request->query('created_to');
        $search = $request->query('q');

        $userId = $request->user()->id_utilisateur;

        $q = Projet::query()
            ->with(['currentSprint', 'firstSprint'])
            ->where(function ($qq) use ($userId) {
                $qq->where('owner_id', $userId)
                    ->orWhereHas('members', fn ($m) =>
                        $m->where('membre_projet.id_utilisateur', $userId)
                    );
            });

        if ($scope === 'owned') {
            $q->where('owner_id', $userId);
        } elseif ($scope === 'shared') {
            $q->where('owner_id', '!=', $userId)
              ->whereHas('members', fn ($m) =>
                  $m->where('membre_projet.id_utilisateur', $userId)
              );
        }

        if ($status) {
            $q->where('status', $status);
        }
        if ($visibility) {
            $q->where('visibility', $visibility);
        }
        if ($createdFrom) {
            $q->whereDate('created_at', '>=', $createdFrom);
        }
        if ($createdTo) {
            $q->whereDate('created_at', '<=', $createdTo);
        }

        if ($search) {
            $q->where('nom', 'like', '%' . $search . '%');
        }

        $projets = $q->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('projects.index', compact(
            'projets',
            'scope',
            'status',
            'visibility',
            'createdFrom',
            'createdTo',
            'search'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Projet::class);

        $projet = new Projet([
            'status'     => 'active',
            'visibility' => 'private',
        ]);

        return view('projects.create', compact('projet'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjetStoreRequest $request)
    {
        $this->authorize('create', Projet::class);

        $data = $request->validated();
        $membersEmails = $data['members_emails'] ?? null;
        unset($data['members_emails']);

        $data['owner_id'] = $request->user()->id_utilisateur;

        if (in_array($data['visibility'], ['shared'])) {
            $data['share_token'] = Str::uuid()->toString();
        }

        $projet = Projet::create($data);

        $this->syncMembersFromEmails($projet, $membersEmails);

        return redirect()
            ->route('projects.index')
            ->with('ok', 'Project created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Projet $projet)
    {
        $this->authorize('view', $projet);

        $projet->load([
            'sprints' => function ($q) {
                $q->with([
                    'epics' => function ($qq) {
                        $qq->with([
                            'taches' => fn ($tq) => $tq->with('assignee'),
                        ])->orderBy('nom');
                    },
                    'taches' => fn ($t) => $t
                        ->with('assignee')
                        ->orderBy('created_at', 'desc'),
                ])->orderBy('start_date');
            },
            'members:id_utilisateur,nom,prenom,email',
            'owner:id_utilisateur,nom,prenom,email',
        ]);

        $assigneeOptions = collect([$projet->owner])
            ->merge($projet->members)
            ->filter()
            ->mapWithKeys(function ($u) {
                return [
                    $u->id_utilisateur => trim(($u->nom ?? '') . ' ' . ($u->prenom ?? '')),
                ];
            })
            ->toArray();

        $assigneeOptions = ['' => '—'] + $assigneeOptions;

        return view('projects.show', [
            'projet' => $projet,
            'assigneeOptions' => $assigneeOptions,
        ]);
    }

    public function shared(string $token)
    {
        $project = Projet::where('share_token', $token)->firstOrFail();

        abort_unless(in_array($project->visibility, ['shared']), 403);

        request()->merge(['token' => $token]);
        $this->authorize('view', $project);

        $project->load([
            'sprints' => fn ($q) => $q->orderBy('start_date'),
            'sprints.epics.taches.assignee',
            'epics.taches.assignee',
        ]);

        return view('projects.show', [
            'projet'   => $project,
            'readonly' => true,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Projet $projet)
    {
        $this->authorize('update', $projet);

        return view('projects.edit', compact('projet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjetUpdateRequest $request, Projet $projet)
    {
        $this->authorize('update', $projet);

        $data = $request->validated();
        $membersEmails = $data['members_emails'] ?? null;
        unset($data['members_emails']);

        if (in_array($data['visibility'], ['shared']) && empty($data['share_token'])) {
            $data['share_token'] = Str::uuid()->toString();
        }
        if ($data['visibility'] === 'private') {
            $data['share_token'] = null;
        }

        $projet->update($data);

        $this->syncMembersFromEmails($projet, $membersEmails);

        return redirect()
            ->route('projects.index')
            ->with('ok', 'Project updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Projet $projet)
    {
        $this->authorize('delete', $projet);
        $projet->delete();

        return redirect()
            ->route('projects.index')
            ->with('ok', 'Project deleted');
    }

    private function syncMembersFromEmails(Projet $project, ?string $rawEmails): void
    {
        if ($rawEmails === null) {
            return;
        }

        $emails = collect(
            preg_split('/[\s,;]+/', $rawEmails, -1, PREG_SPLIT_NO_EMPTY)
        )
            ->map(fn ($e) => strtolower(trim($e)))
            ->filter()
            ->unique();

        if ($emails->isEmpty()) {
            $project->members()->sync([]);

            return;
        }

        $users = Utilisateur::whereIn('email', $emails)->get();

        $knownEmails = $users->pluck('email')->map(fn ($e) => strtolower($e));

        $memberIds = $users->pluck('id_utilisateur')
            ->reject(fn ($id) => $id === $project->owner_id)
            ->values()
            ->all();

        $project->members()->sync($memberIds);

        $unknownEmails = $emails->diff($knownEmails);

        foreach ($unknownEmails as $email) {
            $invitation = ProjectInvitation::create([
                'id_projet' => $project->id_projet,
                'email' => $email,
                'token' => Str::uuid()->toString(),
            ]);

            Mail::to($email)->send(new ProjectInvitationMail($invitation));
        }

        if ($unknownEmails->isNotEmpty()) {
            session()->flash(
                'members_warning',
                'Invitations were sent to: ' . $unknownEmails->join(', ')
            );
        }
    }
}
