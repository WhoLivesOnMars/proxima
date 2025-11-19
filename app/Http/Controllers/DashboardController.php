<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Tache;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $userId = $user->id_utilisateur;

        $projectsQuery = Projet::ownedOrMember($userId);

        $projectsCount = $projectsQuery->count();

        $projectIds = $projectsQuery->pluck('id_projet');

        $tasksAssignedCount = Tache::where('id_utilisateur', $userId)
            ->whereIn('id_projet', $projectIds)
            ->count();

        $sprintsCount = Sprint::whereIn('id_projet', $projectIds)->count();

        $completedTasksCount = Tache::where('id_utilisateur', $userId)
            ->where('status', 'done')
            ->whereIn('id_projet', $projectIds)
            ->count();

        return view('dashboard', [
            'projectsCount' => $projectsCount,
            'tasksAssignedCount' => $tasksAssignedCount,
            'sprintsCount' => $sprintsCount,
            'completedTasksCount' => $completedTasksCount,
        ]);
    }
}
