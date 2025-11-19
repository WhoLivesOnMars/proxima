<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Projet;
use App\Models\Tache;
use Carbon\Carbon;

class ReportsBoard extends Component
{
    public $projects;
    public ?int $currentProjectId = null;
    public $currentProject = null;

    public $stats = [
        'to_do' => 0,
        'in_progress' => 0,
        'done' => 0,
        'overdue' => 0,
    ];

    public function mount()
    {
        $user = auth()->user();

        $this->projects = Projet::ownedOrMember($user->id_utilisateur)
            ->orderBy('created_at', 'desc')
            ->get();

        $first = $this->projects->first();
        if ($first) {
            $this->currentProjectId = $first->id_projet;
            $this->loadStats();
        }
    }

    public function updatedCurrentProjectId()
    {
        $this->loadStats();
    }

    protected function loadStats()
    {
        $this->currentProject = $this->projects
            ->firstWhere('id_projet', $this->currentProjectId);

        if (! $this->currentProject) {
            $this->stats = [
                'to_do' => 0,
                'in_progress' => 0,
                'done' => 0,
                'overdue' => 0,
            ];
            return;
        }

        $base = Tache::where('id_projet', $this->currentProjectId);

        $this->stats['to_do'] = (clone $base)->where('status', 'todo')->count();
        $this->stats['in_progress'] = (clone $base)->where('status', 'in_progress')->count();
        $this->stats['done'] = (clone $base)->where('status', 'done')->count();

        $this->stats['overdue'] = (clone $base)
            ->whereIn('status', ['todo', 'in_progress'])
            ->whereDate('deadline', '<', Carbon::today())
            ->count();
    }

    public function render()
    {
        return view('livewire.reports-board');
    }
}
