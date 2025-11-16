<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use Illuminate\Http\Request;

class KanbanController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $projects = Projet::ownedOrMember($user->id_utilisateur)
            ->with(['sprints' => fn($q) => $q->orderBy('start_date')])
            ->orderBy('nom')
            ->get();

        return view('kanban.index', [
            'projects' => $projects,
        ]);
    }
}
