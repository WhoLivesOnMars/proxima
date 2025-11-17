<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Epic;
use App\Models\Tache;
use Carbon\Carbon;

class RoadmapBoard extends Component
{
    public $projects;
    public ?int $currentProjectId = null;

    public $sprints = [];
    public $epics = [];
    public $grid = [];

    public bool $modalOpen = false;
    public ?int $modalTaskId = null;

    public ?string $modalTitre = null;
    public ?string $modalDescription = null;
    public ?string $modalEpicName = null;
    public ?string $modalSprintName = null;
    public ?string $modalStartDate = null;
    public ?string $modalDeadline = null;
    public ?string $modalAssigneeName = null;
    public ?string $modalStatus = null;
    public ?string $modalAttachmentPath = null;

    public function mount(): void
    {
        $user = auth()->user();

        $this->projects = Projet::ownedOrMember($user->id_utilisateur)
            ->with(['sprints' => fn ($q) => $q->orderBy('start_date')])
            ->orderBy('nom')
            ->get();

        $this->currentProjectId = $this->projects->first()->id_projet ?? null;

        $this->loadRoadmap();
    }

    public function selectProject(int $projectId): void
    {
        $this->currentProjectId = $projectId;
        $this->loadRoadmap();
    }

    protected function loadRoadmap(): void
    {
        if (!$this->currentProjectId) {
            $this->sprints = [];
            $this->epics = [];
            $this->grid = [];
            return;
        }

        $project = Projet::with([
            'sprints' => fn ($q) => $q->orderBy('start_date'),
            'epics' => fn ($q) => $q->orderBy('nom'),
            'taches' => fn ($q) => $q->with(['assignee', 'epic', 'sprint']),
        ])->findOrFail($this->currentProjectId);

        $this->sprints = $project->sprints;
        $this->epics = $project->epics;

        $grid = [];

        foreach ($project->taches as $task) {
            if (!$task->id_epic || !$task->id_sprint) {
                continue;
            }

            $eId = $task->id_epic;
            $sId = $task->id_sprint;

            $grid[$eId][$sId][] = $task;
        }

        $this->grid = $grid;
    }

    public function getSprintProgress(int $sprintId): int
    {
        $tasks = Tache::where('id_sprint', $sprintId)->get();
        $total = $tasks->count();
        if ($total === 0) return 0;

        $done = $tasks->where('status', 'done')->count();

        return (int) round($done / $total * 100);
    }

    public function getEpicProgress(int $epicId): int
    {
        $tasks = Tache::where('id_epic', $epicId)->get();
        $total = $tasks->count();
        if ($total === 0) return 0;

        $done = $tasks->where('status', 'done')->count();

        return (int) round($done / $total * 100);
    }

    public function getTaskSegment(Tache $task, Sprint $sprint): array
    {
        if (!$sprint->start_date || !$sprint->end_date) {
            return ['offset' => 0, 'width' => 100];
        }

        $sStart = $sprint->start_date->copy()->startOfDay();
        $sEnd = $sprint->end_date->copy()->endOfDay();

        $totalDays = max($sStart->diffInDays($sEnd) + 1, 1);

        $tStart = $task->start_date
            ? $task->start_date->copy()->startOfDay()
            : $sStart;

        $tEnd = $task->deadline
            ? $task->deadline->copy()->endOfDay()
            : $sEnd;

        if ($tEnd < $sStart || $tStart > $sEnd) {
            return ['offset' => 0, 'width' => 0];
        }

        if ($tStart < $sStart) $tStart = $sStart;
        if ($tEnd > $sEnd) $tEnd = $sEnd;

        $offsetDays = $sStart->diffInDays($tStart);
        $durationDays = max($tStart->diffInDays($tEnd) + 1, 1);

        $offset = $offsetDays / $totalDays * 100;
        $width = $durationDays / $totalDays * 100;

        $minWidth = 12;
        if ($width < $minWidth) {
            $width = $minWidth;
            if ($offset + $width > 100) {
                $offset = max(0, 100 - $width);
            }
        }

        return [
            'offset' => round($offset, 2),
            'width' => round($width, 2),
        ];
    }

    public function openCard(int $taskId): void
    {
        $t = Tache::with(['epic', 'sprint', 'assignee'])->findOrFail($taskId);

        $this->modalTaskId = $t->id_tache;
        $this->modalTitre = $t->titre ?? '';
        $this->modalDescription = $t->description;
        $this->modalEpicName = $t->epic?->nom;
        $this->modalSprintName = $t->sprint?->nom;
        $this->modalStartDate = $t->start_date ? $t->start_date->format('d/m/Y') : null;
        $this->modalDeadline = $t->deadline ? $t->deadline->format('d/m/Y') : null;
        $this->modalAssigneeName = $t->assignee
            ? trim(($t->assignee->prenom ?? '') . ' ' . ($t->assignee->nom ?? ''))
            : null;

        $this->modalStatus = match ($t->status) {
            'todo' => 'To do',
            'in_progress' => 'In progress',
            'done' => 'Done',
            default => $t->status,
        };

        $this->modalAttachmentPath = $t->attachment_path;

        $this->modalOpen = true;
    }

    public function initials($user): string
    {
        if (!$user) return '—';
        $p = mb_strtoupper(mb_substr($user->prenom ?? '', 0, 1));
        $n = mb_strtoupper(mb_substr($user->nom ?? '', 0, 1));
        return $p . $n;
    }

    public function render()
    {
        $currentProject = $this->projects->firstWhere('id_projet', $this->currentProjectId);

        return view('livewire.roadmap-board', [
            'currentProject' => $currentProject,
            'sprints' => $this->sprints,
            'epics' => $this->epics,
            'grid' => $this->grid,
        ]);
    }
}
