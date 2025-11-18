<?php

namespace App\Livewire;

use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Tache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class KanbanBoard extends Component
{
    use WithFileUploads;

    public $modalAttachment = null;

    public $projects;
    public ?int $currentProjectId = null;

    public $availableSprints;
    public ?int $currentSprintId = null;

    public array $tasksByStatus = [];

    public string $newTitle = '';

    public ?int $openTaskId = null;

    public array $assigneeOptions = [];
    public array $epicOptions = [];

    public array $filters = [
        'assignee' => '',
        'status' => '',
        'date_from' => null,
        'date_to' => null,
    ];

    public bool $modalOpen = false;
    public ?int $modalTaskId = null;
    public string $modalTitre = '';
    public ?string $modalDescription = null;
    public ?string $modalDeadline = null;
    public $modalAssigneeId = null;
    public ?string $modalAttachmentPath = null;
    public $modalEpicId = null;
    public ?string $modalStartDate = null;

    public function mount()
    {
        $user = auth()->user();

        $this->projects = Projet::ownedOrMember($user->id_utilisateur)
            ->with(['sprints' => fn($q) => $q->orderBy('start_date')])
            ->orderBy('nom')
            ->get();

        $this->currentProjectId = $this->projects->first()->id_projet ?? null;

        $this->reloadSprintsAndTasks();
    }

    public function selectProject(int $projectId): void
    {
        $this->currentProjectId = $projectId;
        $this->currentSprintId = null;
        $this->reloadSprintsAndTasks();
    }

    protected function reloadSprintsAndTasks(): void
    {
        if (!$this->currentProjectId) {
            $this->availableSprints = collect();
            $this->tasksByStatus = [];
            $this->assigneeOptions = [];
            $this->epicOptions = [];
            $this->filters = [
                'assignee' => '',
                'status' => '',
                'date_from' => null,
                'date_to' => null,
            ];
            return;
        }

        $project = $this->projects->firstWhere('id_projet', $this->currentProjectId);

        $this->buildAssigneeOptions($project);

        $active = $project->currentSprint;
        $this->availableSprints = $project->sprints;

        if ($active) {
            $this->currentSprintId = $active->id_sprint;
        } else {
            $this->currentSprintId = $project->sprints->first()->id_sprint ?? null;
        }

        $this->buildEpicOptions($project);

        $this->resetFilters();
    }

    protected function buildAssigneeOptions(?Projet $project): void
    {
        $list = [];

        if (!$project) {
            $this->assigneeOptions = [];
            return;
        }

        $owner = $project->owner()
            ->select('utilisateur.id_utilisateur', 'utilisateur.nom', 'utilisateur.prenom')
            ->first();

        if ($owner) {
            $list[$owner->id_utilisateur] = trim(($owner->prenom ?? '') . ' ' . ($owner->nom ?? ''));
        }

        $members = $project->members()
            ->select('utilisateur.id_utilisateur', 'utilisateur.nom', 'utilisateur.prenom')
            ->orderBy('prenom')
            ->orderBy('nom')
            ->get();

        foreach ($members as $m) {
            $list[$m->id_utilisateur] = trim(($м->prenom ?? '') . ' ' . ($м->nom ?? ''));
        }

        $this->assigneeOptions = $list;
    }

    protected function buildEpicOptions(?Projet $project): void
    {
        if (!$project || !$this->currentSprintId) {
            $this->epicOptions = [];
            return;
        }

        $this->epicOptions = DB::table('epic')
            ->join('epic_sprint', 'epic_sprint.id_epic', '=', 'epic.id_epic')
            ->where('epic.id_projet', $project->id_projet)
            ->where('epic_sprint.id_sprint', $this->currentSprintId)
            ->orderBy('epic.nom')
            ->pluck('epic.nom', 'epic.id_epic')
            ->toArray();
    }

    public function applyFilters(): void
    {
        $this->loadTasks();
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'assignee' => '',
            'status' => '',
            'date_from' => null,
            'date_to' => null,
        ];
        $this->loadTasks();
    }

    protected function loadTasks(): void
    {
        $this->tasksByStatus = [
            'todo' => [],
            'in_progress' => [],
            'done' => [],
            'for_approval' => [],
        ];

        if (!$this->currentSprintId) {
            return;
        }

        $q = Tache::with(['epic', 'assignee'])
            ->where('id_sprint', $this->currentSprintId);

        if (!empty($this->filters['assignee'])) {
            $q->where('id_utilisateur', $this->filters['assignee']);
        }

        if (!empty($this->filters['status'])) {
            $q->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['date_from'])) {
            $q->whereDate('start_date', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $q->whereDate('deadline', '<=', $this->filters['date_to']);
        }

        $tasks = $q->orderBy('created_at')->get();

        foreach ($tasks as $task) {
            $status = $task->status ?: 'todo';
            if (!isset($this->tasksByStatus[$status])) {
                $this->tasksByStatus[$status] = [];
            }
            $this->tasksByStatus[$status][] = $task;
        }
    }

    protected function sprintBounds(?Sprint $sprint): ?array
    {
        if (!$sprint || !$sprint->start_date) {
            return null;
        }

        $sprintStart = Carbon::parse($sprint->start_date);

        if (!empty($sprint->end_date)) {
            $sprintEnd = Carbon::parse($sprint->end_date);
        } else {
            $days = (int) ($sprint->duree ?? 0);
            if ($days <= 0) {
                return null;
            }
            $sprintEnd = $sprintStart->copy()->addDays($days - 1);
        }

        if ($sprintEnd->lt($sprintStart)) {
            return null;
        }

        return [$sprintStart, $sprintEnd];
    }

    protected function validateTaskDates(?Carbon $start, ?Carbon $deadline, ?Sprint $sprint): ?string
    {
        if ($start && $deadline && $deadline->lt($start)) {
            return 'Deadline cannot be earlier than the start date.';
        }

        $bounds = $this->sprintBounds($sprint);
        if (!$bounds) {
            return null;
        }

        [$sprintStart, $sprintEnd] = $bounds;

        if ($start) {
            if ($start->lt($sprintStart)) {
                return 'Start date cannot be earlier than the sprint start.';
            }
            if ($start->gt($sprintEnd)) {
                return 'Start date must be within the sprint dates.';
            }
        }

        if ($deadline) {
            if ($deadline->lt($sprintStart) || $deadline->gt($sprintEnd)) {
                return 'Deadline must be within the sprint dates.';
            }
        }

        return null;
    }

    public function storeNewTask(string $status = 'todo'): void
    {
        if (!$this->currentProjectId || !$this->currentSprintId) return;

        $titre = trim($this->newTitle) ?: 'New task';

        $sprint = Sprint::find($this->currentSprintId);

        $start  = $sprint?->start_date ? Carbon::parse($sprint->start_date) : now();
        $bounds = $this->sprintBounds($sprint);

        if ($bounds) {
            [, $sprintEnd] = $bounds;
            $end = $sprintEnd;
        } else {
            $end = $start->copy()->addDays(6);
        }

        Tache::create([
            'id_projet' => $this->currentProjectId,
            'id_sprint' => $this->currentSprintId,
            'id_utilisateur' => null,
            'titre'=> $titre,
            'start_date' => $start->toDateString(),
            'deadline' => $end->toDateString(),
            'status' => $status,
        ]);

        $this->newTitle = '';
        $this->loadTasks();
    }

    public function moveTaskWithRules(int $taskId, string $targetStatus): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $current = $task->status ?: 'todo';

        if ($current === 'todo' && $targetStatus === 'done') {
            $this->dispatch('kanban-error', message: 'You can’t move a task directly from "To do" to "Done". Move it to "In progress" first.');
            return;
        }

        if ($targetStatus === 'for_approval' && $current !== 'done') {
            $this->dispatch('kanban-error', message: 'You can send a task to "For approval" only from "Done".');
            return;
        }

        $task->status = $targetStatus;
        $task->save();

        $this->loadTasks();
    }

    public function updateTitle(int $taskId, string $value): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $task->titre = trim($value) ?: 'Untitled';
        $task->save();
        $this->loadTasks();
    }

    public function updateDeadline(int $taskId, ?string $value): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $start = $task->start_date ? Carbon::parse($task->start_date) : null;
        $deadline = $value ? Carbon::parse($value) : null;
        $sprint = Sprint::find($task->id_sprint);

        $error = $this->validateTaskDates($start, $deadline, $sprint);
        if ($error) {
            $this->dispatch('kanban-error', message: $error);
            return;
        }

        $task->deadline = $value ?: null;
        $task->save();
        $this->loadTasks();
    }

    public function assignTo(int $taskId, int $userId): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $task->id_utilisateur = $userId;
        $task->save();

        $this->loadTasks();
        $this->dispatch('kanban-info', message: 'Assignee updated.');
    }

    public function copyTask(int $taskId): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $copy = $task->replicate();
        $copy->titre = 'Copy of ' . $task->titre;
        $copy->status = 'todo';
        $copy->created_at = now();
        $copy->updated_at = now();
        $copy->save();

        $this->loadTasks();
        $this->dispatch('kanban-info', message: 'Card copied.');
    }

    public function deleteTask(int $taskId): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('delete', $task);

        $task->delete();
        $this->loadTasks();
    }

    public function openCard(int $taskId): void
    {
        $t = Tache::with('assignee')->findOrFail($taskId);
        Gate::authorize('view', $t);

        $this->modalTaskId = $t->id_tache;
        $this->modalTitre = $t->titre ?? '';
        $this->modalDescription = $t->description;
        $this->modalDeadline = $t->deadline?->format('Y-m-d');
        $this->modalAssigneeId  = $t->id_utilisateur;

        $this->modalEpicId = $t->id_epic;
        $this->modalStartDate = $t->start_date
            ? Carbon::parse($t->start_date)->format('Y-m-d')
            : null;

        $this->modalAttachmentPath = $t->attachment_path;
        $this->modalAttachment = null;

        $this->modalOpen = true;
    }

    public function saveCard(): void
    {
        if (!$this->modalTaskId) return;

        $t = Tache::findOrFail($this->modalTaskId);
        Gate::authorize('update', $t);

        $newStart = $this->modalStartDate !== null && $this->modalStartDate !== ''
            ? $this->modalStartDate
            : null;

        $newDeadline = $this->modalDeadline !== null && $this->modalDeadline !== ''
            ? $this->modalDeadline
            : null;

        $start    = $newStart ? Carbon::parse($newStart) : null;
        $deadline = $newDeadline ? Carbon::parse($newDeadline) : null;

        $sprint = Sprint::find($t->id_sprint);

        $error = $this->validateTaskDates($start, $deadline, $sprint);
        if ($error) {
            $this->dispatch('kanban-error', message: $error);
            return;
        }

        $assigneeId = $this->modalAssigneeId;
        if ($assigneeId === '' || $assigneeId === null || $assigneeId === 'null') {
            $assigneeId = null;
        } else {
            $assigneeId = (int) $assigneeId;
        }

        $epicId = $this->modalEpicId;
        if ($epicId === '' || $epicId === null || $epicId === 'null') {
            $epicId = null;
        } else {
            $epicId = (int) $epicId;
        }

        $data = [
            'titre' => trim($this->modalTitre) ?: 'Untitled',
            'description' => $this->modalDescription,
            'id_epic' => $epicId,
            'id_utilisateur' => $assigneeId,
            'start_date' => $newStart,
            'deadline' => $newDeadline,
        ];

        $oldPath = $t->attachment_path;

        if ($this->modalAttachment) {
            try {
                $this->validate([
                    'modalAttachment' => 'file|max:5120|mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx,ppt,pptx,txt',
                ]);
            } catch (ValidationException $e) {
                $this->dispatch('kanban-error', message: 'File must be PDF, image or Office document up to 5 MB.');
                throw $e;
            }

            $clientName = $this->modalAttachment->getClientOriginalName();
            $ext = $this->modalAttachment->getClientOriginalExtension();
            $base = pathinfo($clientName, PATHINFO_FILENAME);

            $slugBase = Str::slug($base);
            $safeName = $slugBase ?: 'file';

            $fileName = $this->modalTaskId . '-' . $safeName . '.' . $ext;

            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $this->modalAttachment->storeAs('attachments', $fileName, 'public');

            $data['attachment_path']   = $path;
            $this->modalAttachmentPath = $path;
        }

        $t->update($data);

        $this->modalAttachment = null;

        $this->modalOpen = false;

        $this->loadTasks();
        $this->dispatch('kanban-info', message: 'Card saved.');
    }

    public function deleteAttachment(): void
    {
        if (!$this->modalTaskId) return;

        $t = Tache::findOrFail($this->modalTaskId);
        Gate::authorize('update', $t);

        if ($t->attachment_path) {
            Storage::disk('public')->delete($t->attachment_path);
            $t->update(['attachment_path' => null]);
        }

        $this->modalAttachment     = null;
        $this->modalAttachmentPath = null;

        $this->loadTasks();

        $this->dispatch('kanban-info', message: 'Attachment deleted.');
    }

    public function initials($user): string
    {
        if (!$user) return '—';
        $p = Str::of($user->prenom ?? '')->substr(0, 1)->upper();
        $n = Str::of($user->nom ?? '')->substr(0, 1)->upper();
        return $p . $n;
    }

    public function render()
    {
        $currentProject = $this->projects->firstWhere('id_projet', $this->currentProjectId);
        $currentSprint  = null;

        if ($currentProject) {
            $currentSprint = $currentProject->sprints->firstWhere('id_sprint', $this->currentSprintId);
        }

        return view('livewire.kanban-board', [
            'currentProject' => $currentProject,
            'currentSprint' => $currentSprint,
        ]);
    }
}
