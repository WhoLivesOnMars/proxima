<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Projet;
use App\Models\Tache;
use Carbon\Carbon;

class ReportsBoard extends Component
{
    public $projects;

    #[Url(as: 'project', except: null)]
    public ?int $currentProjectId = null;

    public $currentProject = null;

    public $stats = [
        'to_do' => 0,
        'in_progress' => 0,
        'done' => 0,
        'overdue' => 0,
    ];

    public function mount(): void
    {
        $user = auth()->user();

        $this->projects = Projet::ownedOrMember($user->id_utilisateur)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($this->currentProjectId === null) {
            $this->currentProjectId = $this->projects->first()->id_projet ?? null;
        } else {
            if (! $this->projects->contains('id_projet', $this->currentProjectId)) {
                $this->currentProjectId = $this->projects->first()->id_projet ?? null;
            }
        }

        if ($this->currentProjectId) {
            $this->loadStats();
        }
    }

    public function updatedCurrentProjectId(): void
    {
        if (! $this->projects->contains('id_projet', $this->currentProjectId)) {
            return;
        }

        $this->loadStats();
    }

    protected function loadStats(): void
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
