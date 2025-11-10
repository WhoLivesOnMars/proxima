<?php

namespace App\Http\Controllers;

use App\Models\Sprint;
use App\Models\Projet;
use Illuminate\Http\Request;
use App\Http\Requests\SprintRequest;

class SprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sprints = Sprint::with('projet')->orderByDesc('start_date')->paginate(20);
        return view('sprints.index', compact('sprints'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Projet $projet)
    {
        $project = $projet;
        return view('sprints.create', compact('project'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Projet $projet, SprintRequest $request)
    {
        $data = $request->validated();
        $data['id_projet'] = $projet->id_projet;

        Sprint::create($data);

        return redirect()
            ->route('projects.show', $projet)
            ->with('ok', 'Sprint created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sprint $sprint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sprint $sprint)
    {
        return view('sprints.edit', compact('sprint'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SprintRequest $request, Sprint $sprint)
    {
        $data = $request->validated();
        $data['id_projet'] = $sprint->id_projet;

        $sprint->update($data);

        return redirect()
            ->route('projects.show', $sprint->id_projet)
            ->with('ok', 'Sprint updated.');
    }

    public function inline(Request $request, Sprint $sprint)
    {
        $this->authorize('update', $sprint);

        $data = $request->validate([
            'nom' => ['sometimes','string','max:255'],
            'start_date' => ['sometimes','date'],
            'duree' => ['sometimes','integer','min:1','max:365'],
        ]);

        $sprint->fill($data)->save();

        return response()->json(['ok' => true, 'sprint' => $sprint->fresh()]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sprint $sprint)
    {
        $project = $sprint->projet;
        $sprint->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('ok', 'Sprint deleted.');
    }
}
