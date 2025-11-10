<?php

namespace App\Http\Controllers;

use App\Models\Epic;
use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Tache;
use App\Models\TacheProposee;
use App\Http\Requests\TacheStoreRequest;
use App\Http\Requests\TacheUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Projet::query()
            ->where(function ($q) use ($user) {
                $q->where('owner_id', $user->id_utilisateur)
                ->orWhereHas('members', fn($m) =>
                    $m->where('membre_projet.id_utilisateur', $user->id_utilisateur)
                );
            })
            ->orderBy('nom')
        ->get(['id_projet','nom']);

        $current = $request->input('project', 'all');

        if ($current !== 'all') {
            $project = Projet::query()
                ->with([
                    'sprints' => fn($q)=>$q->orderBy('start_date'),
                    'epics' => fn($q)=>$q->orderBy('nom'),
                    'taches' => fn($q)=>$q->with('assignee')->orderBy('created_at','desc'),
                ])
                ->findOrFail($current);

            $this->authorize('view', $project);

            return view('tasks.index', [
                'projects' => $projects,
                'current' => $current,
                'grouped' => true,
                'project' => $project,
            ]);
        }

        $tasks = Tache::with([
                'projet:id_projet,nom',
                'sprint:id_sprint,nom',
                'epic:id_epic,nom',
                'assignee:id_utilisateur,nom,prenom',
            ])
            ->where(function ($q) use ($user) {
                $q->where('id_utilisateur', $user->id_utilisateur)
                    ->orWhereIn('id_projet', function ($sub) use ($user) {
                        $sub->from('membre_projet')->select('id_projet')
                            ->where('id_utilisateur', $user->id_utilisateur);
                    });
            })
            ->when($request->filled('status'), fn($q)=>$q->where('status', $request->status))
            ->when($request->filled('assignee'), fn($q)=>$q->whereHas('assignee', function($qq) use ($request){
                $term = '%'.$request->assignee.'%';
                $qq->where('nom', 'like', $term)->orWhere('prenom','like',$term);
            }))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('tasks.index', [
            'projects' => $projects,
            'current' => 'all',
            'grouped' => false,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Projet $projet, Request $request)
    {
        $this->authorize('create', [Tache::class, $projet]);

        $sprints = Sprint::where('id_projet', $projet->id_projet)
            ->with(['epics:id_epic,nom'])
            ->orderBy('start_date')
            ->get(['id_sprint','nom','start_date']);

        $epicsBySprint = $sprints->mapWithKeys(function ($s) {
            return [
                $s->id_sprint => $s->epics->map(fn($e) => [
                    'id'  => $e->id_epic,
                    'nom' => $e->nom,
                ])->values(),
            ];
        });

        return view('tasks.create', [
            'project' => $projet,
            'sprints' => $sprints,
            'epicsBySprint' => $epicsBySprint,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Projet $projet, TacheStoreRequest $request)
    {
        $this->authorize('create', [Tache::class, $projet]);

        $data = $request->validated();
        $pid = $projet->id_projet;

        $sprintBelongs = Sprint::where('id_sprint', $data['id_sprint'] ?? null)
            ->where('id_projet', $pid)->exists();
        abort_unless($sprintBelongs, 422, 'Sprint does not belong to project.');

        if (!empty($data['id_epic'])) {
            $epicBelongs = Epic::where('id_epic', $data['id_epic'])
                ->where('id_projet', $pid)->exists();
            abort_unless($epicBelongs, 422, 'Epic does not belong to project.');
        }

        $user = $request->user();
        $isOwner  = $projet->owner_id === $user->id_utilisateur;
        $isMember = $projet->members()
            ->wherePivot('id_utilisateur', $user->id_utilisateur)->exists();

        if ($isOwner) {
            Tache::create([
                'id_projet' => $pid,
                'id_epic' => $data['id_epic'] ?? null,
                'id_sprint' => $data['id_sprint'],
                'id_utilisateur' => $data['id_utilisateur'] ?? $user->id_utilisateur,
                'titre' => $data['titre'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'] ?? ($sprint?->start_date),
                'deadline' => $data['deadline'] ?? null,
                'status' => $data['status'],
            ]);

            return redirect()->route('projects.show', $projet)
                ->with('ok', 'Task created.');
        }

        if ($isMember) {
            TacheProposee::create([
                'id_projet' => $pid,
                'created_by' => $user->id_utilisateur,
                'id_utilisateur' => $data['id_utilisateur'] ?? null,
                'id_epic' => $data['id_epic'] ?? null,
                'id_sprint' => $data['id_sprint'] ?? null,
                'titre' => $data['titre'],
                'description' => $data['description'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'approval' => 'pending',
            ]);

            return redirect()
                ->route('projects.proposals.index', $projet)
                ->with('ok', 'Task proposal submitted for approval.');
        }

        abort(403);
    }

    public function proposalsIndex(Projet $projet, Request $request)
    {
        $this->authorize('viewProposals', [Tache::class, $projet]);

        $user = $request->user();

        $q = TacheProposee::with(['creator','assignee','epic','sprint'])
            ->where('id_projet', $projet->id_projet)
            ->orderByDesc('created_at');

        if ($projet->owner_id !== $user->id_utilisateur) {
            $q->where('created_by', $user->id_utilisateur);
        }

        $proposals = $q->paginate(20)->withQueryString();

        return view('tasks.proposals.index', compact('projet','proposals'));
    }

    public function propose(Projet $projet, Request $request)
    {
        $this->authorize('propose', [Tache::class, $projet]);

        $data = $request->validate([
            'titre' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'deadline' => ['nullable','date'],
            'id_sprint' => ['nullable','exists:sprint,id_sprint'],
            'id_epic' => ['nullable','exists:epic,id_epic'],
            'id_utilisateur' => ['nullable','exists:utilisateur,id_utilisateur'],
        ]);

        if (!empty($data['id_sprint'])) {
            abort_unless(
                Sprint::where('id_sprint', $data['id_sprint'])
                    ->where('id_projet', $projet->id_projet)->exists(),
                422, 'Sprint does not belong to project.'
            );
        }
        if (!empty($data['id_epic'])) {
            abort_unless(
                Epic::where('id_epic', $data['id_epic'])
                    ->where('id_projet', $projet->id_projet)->exists(),
                422, 'Epic does not belong to project.'
            );
        }

        TacheProposee::create([
            'id_projet' => $projet->id_projet,
            'created_by' => $request->user()->id_utilisateur,
            'id_utilisateur' => $data['id_utilisateur'] ?? null,
            'id_epic' => $data['id_epic'] ?? null,
            'id_sprint' => $data['id_sprint'] ?? null,
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'deadline' => $data['deadline'] ?? null,
            'approval' => 'pending',
        ]);

        return back()->with('ok','Task proposal submitted for approval.');
    }

    public function approveProposal(TacheProposee $tache_proposee, Request $request)
    {
        $this->authorize('moderate', $tache_proposee);

        DB::transaction(function () use ($tache_proposee, $request) {
            Tache::create([
                'id_projet' => $tache_proposee->id_projet,
                'id_epic' => $tache_proposee->id_epic ?? null,
                'id_sprint' => $tache_proposee->id_sprint ?? $this->inferCurrentSprint($tache_proposee->id_projet),
                'id_utilisateur' => $tache_proposee->id_utilisateur ?? $tache_proposee->created_by,
                'titre' => $tache_proposee->titre,
                'description' => $tache_proposee->description,
                'deadline' => $tache_proposee->deadline,
                'status' => 'todo',
            ]);

            $tache_proposee->update([
                'approval' => 'approved',
                'decided_by' => $request->user()->id_utilisateur,
                'decided_at' => now(),
                'decided_comment' => $request->input('comment'),
            ]);
        });

        return back()->with('ok','Task approved and added to project.');
    }

    public function rejectProposal(TacheProposee $tache_proposee, Request $request)
    {
        $this->authorize('moderate', $tache_proposee);

        $tache_proposee->update([
            'approval' => 'rejected',
            'decided_by' => $request->user()->id_utilisateur,
            'decided_at' => now(),
            'decided_comment' => $request->input('comment'),
        ]);

        return back()->with('ok','Task proposal rejected.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tache $tache)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tache $tache)
    {
        $this->authorize('update', $tache);

        $project = Projet::findOrFail($tache->id_projet);
        $sprints = Sprint::where('id_projet', $project->id_projet)->orderBy('start_date')->get(['id_sprint', 'nom']);
        $epics = Epic::where('id_projet', $project->id_projet)->orderBy('nom')->get(['id_epic', 'nom']);

        return view('tasks.edit', compact('tache', 'project', 'sprints', 'epics'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TacheUpdateRequest $request, Tache $tache)
    {
        $this->authorize('update', $tache);

        $data = $request->validated();
        $pid = $tache->id_projet;

        $sOk = Sprint::where('id_sprint', $data['id_sprint'])
            ->where('id_projet', $pid)->exists();
        abort_unless($sOk, 422, 'Sprint must belong to the same project as task.');

        if (!empty($data['id_epic'])) {
            $eOk = Epic::where('id_epic', $data['id_epic'])
                ->where('id_projet', $pid)->exists();
            abort_unless($eOk, 422, 'Epic must belong to the same project as task.');
        }

        $tache->update($data);

        return redirect()->route('projects.show', $tache->id_projet)
            ->with('ok', 'Task updated.');
    }

    public function inline(Request $request, Tache $tache)
    {
        $this->authorize('update', $tache);

        $pid = $tache->id_projet;

        $data = $request->validate([
            'titre' => ['sometimes','string','max:255'],
            'status' => ['sometimes', Rule::in(['todo','in_progress','done'])],
            'deadline' => ['sometimes','nullable','date'],
            'start_date' => ['sometimes','nullable','date'],
            'id_sprint' => ['sometimes','exists:sprint,id_sprint'],
            'id_epic' => ['sometimes','nullable','exists:epic,id_epic'],
            'id_utilisateur'=> ['sometimes','nullable','exists:utilisateur,id_utilisateur'],
        ]);

        if (array_key_exists('id_sprint', $data)) {
            abort_unless(
                Sprint::where('id_sprint', $data['id_sprint'])->where('id_projet', $pid)->exists(),
                422, 'Sprint must belong to the same project.'
            );
        }
        if (array_key_exists('id_epic', $data) && !empty($data['id_epic'])) {
            abort_unless(
                Epic::where('id_epic', $data['id_epic'])->where('id_projet', $pid)->exists(),
                422, 'Epic must belong to the same project.'
            );
        }

        $tache->fill($data)->save();

        return response()->json(['ok' => true, 'task' => $tache->fresh(['assignee'])]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tache $tache)
    {
        $this->authorize('delete', $tache);
        $tache->delete();

        return back()->with('ok', 'Task deleted');
    }

    private function ensureUnassignedEpic(int $projectId): int
    {
        $unassigned = Epic::firstOrCreate(
            ['id_projet' => $projectId, 'nom' => 'Unassigned'],
            []
        );
        return $unassigned->id_epic;
    }

    private function inferCurrentSprint(int $projectId): int
    {
        $s = Sprint::where('id_projet', $projectId)->orderByDesc('start_date')->first();
        if ($s) return $s->id_sprint;

        $s = Sprint::create([
            'id_projet'  => $projectId,
            'nom' => 'Backlog',
            'start_date' => now()->toDateString(),
            'duree' => 14,
        ]);
        return $s->id_sprint;
    }
}
