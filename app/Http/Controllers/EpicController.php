<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Epic;
use Illuminate\Http\Request;
use App\Http\Requests\EpicStoreRequest;
use App\Http\Requests\EpicUpdateRequest;

class EpicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Projet $projet)
    {
        $project = $projet;
        $sprints = Sprint::where('id_projet', $projet->id_projet)
            ->orderBy('start_date')
            ->get(['id_sprint','nom','start_date']);

        return view('epics.create', compact('project','sprints'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Projet $projet, EpicStoreRequest $request)
    {
        $data = $request->validated();
        $data['id_projet'] = $projet->id_projet;

        $epic = Epic::create([
            'id_projet' => $data['id_projet'],
            'nom'       => $data['nom'],
        ]);
        $epic->sprints()->attach($data['id_sprint'], ['id_projet' => $data['id_projet']]);

        return redirect()->route('projects.show', $projet)->with('ok', 'Epic created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Epic $epic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Epic $epic)
    {
        $project = $epic->projet;
        $this->authorize('manage', $project);

        $sprints = Sprint::where('id_projet', $project->id_projet)
            ->orderBy('start_date')->get(['id_sprint','nom','start_date']);

        return view('epics.edit', compact('epic','project','sprints'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EpicUpdateRequest $request, Epic $epic)
    {
        $project = $epic->projet;
        $this->authorize('manage', $project);

        $data = $request->validated();

        if (isset($data['nom'])) {
            $epic->update(['nom' => $data['nom']]);
        }

        if ($request->filled('id_sprint')) {
            $belongs = Sprint::where('id_sprint', $request->id_sprint)
                ->where('id_projet', $project->id_projet)->exists();
            abort_unless($belongs, 422, 'Sprint does not belong to project.');

            $epic->sprints()->sync([
                $request->id_sprint => ['id_projet' => $project->id_projet]
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('ok', 'Epic updated.');
    }

    public function inline(Request $request, Epic $epic)
    {
        $this->authorize('update', $epic);

        $data = $request->validate([
            'nom' => ['sometimes','string','max:255'],
        ]);

        $epic->fill($data)->save();

        return response()->json(['ok' => true, 'epic' => $epic->fresh()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Epic $epic)
    {
        $project = $epic->projet;
        $this->authorize('manage', $project);

        $epic->sprints()->detach();

        if (method_exists($project, 'ensureUnassignedEpic')) {
            $unassignedId = $project->ensureUnassignedEpic($project->id_projet);
            $epic->taches()->update(['id_epic' => $unassignedId]);
        }

        $epic->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('ok', 'Epic deleted.');
    }
}
