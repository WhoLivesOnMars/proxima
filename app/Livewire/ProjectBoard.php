<?php

namespace App\Livewire;

use App\Models\Projet;
use App\Models\Epic;
use App\Models\Tache;
use App\Models\Sprint;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ProjectBoard extends Component
{
    public int $projetId;
    public Projet $projet;

    public ?int $sprintScope = null;

    public string $taskSearch = '';

    public array $filters = [
        'assignee' => '',
        'status' => '',
        'date_from' => '',
        'date_to' => '',
    ];

    public array $appliedFilters = [];

    protected array $editableModels = [
        'sprint' => Sprint::class,
        'epic' => Epic::class,
        'task' => Tache::class,
    ];

    public function mount(int $projetId): void
    {
        $this->projetId = $projetId;
        $this->loadData();

        $this->appliedFilters = $this->filters;
    }

    public function setSprintScope($sprintId): void
    {
        $sprintId = $sprintId === '' ? null : $sprintId;

        $this->sprintScope = $sprintId ? (int) $sprintId : null;
        $this->loadData();
    }

    public function applyFilters(): void
    {
        $this->appliedFilters = [
            'assignee' => $this->filters['assignee']  ?? '',
            'status' => $this->filters['status']    ?? '',
            'date_from' => $this->filters['date_from'] ?? '',
            'date_to' => $this->filters['date_to']   ?? '',
        ];
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'assignee' => '',
            'status' => '',
            'date_from' => '',
            'date_to' => '',
        ];
        $this->appliedFilters = $this->filters;
    }

    public function applySearch(): void
    {
        $this->taskSearch = trim($this->taskSearch ?? '');
    }

    public function loadData(): void
    {
        $this->projet = Projet::with([
            'sprints' => fn ($q) => $q->orderBy('start_date'),
            'sprints.epics' => fn ($q) => $q->orderBy('nom'),
            'sprints.taches' => fn ($q) => $q->orderBy('start_date'),
            'sprints.epics.taches' => fn ($q) => $q->orderBy('start_date'),
        ])->findOrFail($this->projetId);
    }

    public function updateField(string $model, int $id, string $field, $value): void
    {
        $cls = $this->editableModels[$model] ?? null;
        if (!$cls) {
            return;
        }

        $record = $cls::findOrFail($id);
        Gate::authorize('update', $record);

        if (is_string($value)) {
            $value = trim($value);
        }
        if ($value === '') {
            $value = null;
        }

        if ($model === 'sprint' && $field === 'nom' && $value) {
            $exists = Sprint::where('id_projet', $this->projetId)
                ->whereRaw('LOWER(TRIM(nom)) = ?', [mb_strtolower($value)])
                ->where('id_sprint', '!=', $id)
                ->exists();

            if ($exists) {
                $this->dispatch('inline-error', message: 'Sprint name must be unique in this project.');
                return;
            }
        }

        if ($model === 'epic' && $field === 'nom' && $value) {
            $pivot = DB::table('epic_sprint')
                ->where('id_epic', $id)
                ->where('id_projet', $this->projetId)
                ->first();

            $duplicate = false;

            if ($pivot) {
                $epicIdsInSprint = DB::table('epic_sprint')
                    ->where('id_projet', $this->projetId)
                    ->where('id_sprint', $pivot->id_sprint)
                    ->pluck('id_epic');

                $duplicate = Epic::whereIn('id_epic', $epicIdsInSprint)
                    ->whereRaw('LOWER(TRIM(nom)) = ?', [mb_strtolower($value)])
                    ->where('id_epic', '!=', $id)
                    ->exists();
            }

            if ($duplicate) {
                $this->dispatch('inline-error', message: 'Epic name must be unique inside this sprint.');
                return;
            }
        }

        if ($model === 'sprint' && $field === 'start_date') {
            if (!$value) {
                return;
            }

            $record->start_date = Carbon::parse($value)->toDateString();
            $record->save();

            [$sStart, $sEnd] = $this->sprintDateRange($record);

            Tache::where('id_sprint', $record->id_sprint)
                ->get()
                ->each(function (Tache $t) use ($sStart, $sEnd) {
                    if ($t->start_date) {
                        $taskStart = Carbon::parse($t->start_date);
                        if ($sStart && $taskStart->lt($sStart)) {
                            $t->start_date = $sStart->toDateString();
                        } elseif ($sEnd && $taskStart->gt($sEnd)) {
                            $t->start_date = $sEnd->toDateString();
                        }
                    } elseif ($sStart) {
                        $t->start_date = $sStart->toDateString();
                    }

                    if ($t->deadline) {
                        $taskEnd = Carbon::parse($t->deadline);
                        if ($sStart && $taskEnd->lt($sStart)) {
                            $t->deadline = $sStart->toDateString();
                        } elseif ($sEnd && $taskEnd->gt($sEnd)) {
                            $t->deadline = $sEnd->toDateString();
                        }
                    } elseif ($sEnd) {
                        $t->deadline = $sEnd->toDateString();
                    }

                    if ($t->deadline && $t->start_date && $t->deadline < $t->start_date) {
                        $t->deadline = $t->start_date;
                    }

                    $t->save();
                });

            $this->loadData();
            return;
        }

        if ($model === 'sprint' && $field === 'end_date') {
            if (!$value) {
                return;
            }

            $start  = $record->start_date ? Carbon::parse($record->start_date) : null;
            $newEnd = Carbon::parse($value);

            if ($start && $newEnd->lt($start)) {
                $this->dispatch('inline-error', message: 'Sprint end date cannot be before start date.');
                return;
            }

            if ($start) {
                $days = $start->diffInDays($newEnd) + 1;
                $record->duree = max($days, 1);
                $record->save();
            }

            [$sStart, $sEnd] = $this->sprintDateRange($record);

            Tache::where('id_sprint', $record->id_sprint)
                ->get()
                ->each(function (Tache $t) use ($sStart, $sEnd) {

                    if ($t->start_date) {
                        $taskStart = Carbon::parse($t->start_date);
                        if ($sStart && $taskStart->lt($sStart)) {
                            $t->start_date = $sStart->toDateString();
                        } elseif ($sEnd && $taskStart->gt($sEnd)) {
                            $t->start_date = $sEnd->toDateString();
                        }
                    } elseif ($sStart) {
                        $t->start_date = $sStart->toDateString();
                    }

                    if ($t->deadline) {
                        $taskEnd = Carbon::parse($t->deadline);
                        if ($sStart && $taskEnd->lt($sStart)) {
                            $t->deadline = $sStart->toDateString();
                        } elseif ($sEnd && $taskEnd->gt($sEnd)) {
                            $t->deadline = $sEnd->toDateString();
                        }
                    } elseif ($sEnd) {
                        $t->deadline = $sEnd->toDateString();
                    }

                    if ($t->deadline && $t->start_date && $t->deadline < $t->start_date) {
                        $t->deadline = $t->start_date;
                    }

                    $t->save();
                });

            $this->loadData();
            return;
        }

        if ($model === 'task' && $field === 'start_date' && $value) {
            $sprint = Sprint::find($record->id_sprint);
            [$sStart, $sEnd] = $this->sprintDateRange($sprint);
            $newStart = Carbon::parse($value)->toDateString();

            if (($sStart && $newStart < $sStart->toDateString()) || ($sEnd && $newStart > $sEnd->toDateString())) {
                $this->dispatch('inline-error', message: 'Start date must be within the sprint dates.');
                return;
            }

            if ($record->deadline && $newStart > $record->deadline) {
                $this->dispatch('inline-error', message: 'Start date cannot be after deadline.');
                return;
            }

            $value = $newStart;
        }

        if ($model === 'task' && $field === 'deadline' && $value) {
            $sprint = Sprint::find($record->id_sprint);
            [$sStart, $sEnd] = $this->sprintDateRange($sprint);
            $newDeadline = Carbon::parse($value)->toDateString();

            if (($sStart && $newDeadline < $sStart->toDateString()) || ($sEnd && $newDeadline > $sEnd->toDateString())) {
                $this->dispatch('inline-error', message: 'Deadline must be within the sprint dates.');
                return;
            }

            if ($record->start_date && $newDeadline < $record->start_date) {
                $this->dispatch('inline-error', message: 'Deadline cannot be before start date.');
                return;
            }

            $value = $newDeadline;
        }

        if (in_array($field, ['start_date', 'deadline'], true) && $value) {
            $value = Carbon::parse($value)->toDateString();
        }

        try {
            $record->setAttribute($field, $value);
            $record->save();
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                $this->dispatch('inline-error', message: 'This value is already used.');
                return;
            }
            throw $e;
        }

        $this->loadData();
    }

    protected function sprintDateRange(?Sprint $sprint): array
    {
        if (!$sprint || !$sprint->start_date) {
            return [null, null];
        }

        $start = Carbon::parse($sprint->start_date);
        $raw   = (int) $sprint->duree;

        $days = ($raw > 0 && $raw <= 6) ? $raw * 7 : $raw;
        if ($days < 1) {
            $days = 1;
        }

        $end = $start->copy()->addDays($days - 1);

        return [$start, $end];
    }

    public function moveEpicToSprint(int $epicId, int $targetSprintId): void
    {
        $epic = Epic::findOrFail($epicId);
        Gate::authorize('update', $epic);

        $epicName = trim($epic->nom);

        $epicIdsInTargetSprint = DB::table('epic_sprint')
            ->where('id_projet', $this->projetId)
            ->where('id_sprint', $targetSprintId)
            ->pluck('id_epic');

        $duplicate = Epic::whereIn('id_epic', $epicIdsInTargetSprint)
            ->whereRaw('LOWER(TRIM(nom)) = ?', [mb_strtolower($epicName)])
            ->where('id_epic', '!=', $epicId)
            ->exists();

        if ($duplicate) {
            $this->dispatch('inline-error', message: 'This sprint already has an epic with the same name.');
            return;
        }

        DB::table('epic_sprint')
            ->where('id_epic', $epicId)
            ->where('id_projet', $this->projetId)
            ->update(['id_sprint' => $targetSprintId]);

        $targetSprint = Sprint::find($targetSprintId);
        [$sStart, $sEnd] = $this->sprintDateRange($targetSprint);

        Tache::where('id_epic', $epicId)->update([
            'id_sprint' => $targetSprintId,
            'start_date' => $sStart?->toDateString(),
            'deadline' => $sEnd?->toDateString(),
        ]);

        $this->loadData();
    }

    public function moveTaskToEpic(int $taskId, int $targetEpicId): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $pivot = DB::table('epic_sprint')
            ->where('id_epic', $targetEpicId)
            ->where('id_projet', $this->projetId)
            ->first();

        if (!$pivot) {
            $this->dispatch('inline-error', message: 'This epic is not linked to any sprint.');
            return;
        }

        $targetSprintId = (int) $pivot->id_sprint;

        if ((int) $task->id_sprint === $targetSprintId) {
            $task->id_epic = $targetEpicId;
            $task->id_sprint = $targetSprintId;
            $task->save();
        } else {
            $targetSprint = Sprint::find($targetSprintId);
            [$sStart, $sEnd] = $this->sprintDateRange($targetSprint);

            $task->id_epic = $targetEpicId;
            $task->id_sprint = $targetSprintId;
            $task->start_date = $sStart?->toDateString();
            $task->deadline = $sEnd?->toDateString();
            $task->save();
        }

        $this->loadData();
    }

    public function moveTaskToSprint(int $taskId, int $targetSprintId): void
    {
        $task = Tache::findOrFail($taskId);
        Gate::authorize('update', $task);

        $targetSprint = Sprint::find($targetSprintId);
        [$sStart, $sEnd] = $this->sprintDateRange($targetSprint);

        $task->id_sprint = $targetSprintId;
        $task->id_epic = null;
        $task->start_date = $sStart?->toDateString();
        $task->deadline = $sEnd?->toDateString();
        $task->save();

        $this->loadData();
    }

    protected function filtersAreEmpty(): bool
    {
        $f = $this->appliedFilters;

        return
            ($f['assignee'] ?? '') === '' &&
            ($f['status'] ?? '') === '' &&
            ($f['date_from'] ?? '') === '' &&
            ($f['date_to'] ?? '') === '' &&
            trim($this->taskSearch) === '';
    }

    public function sprintMatchesFilters(Sprint $sprint): bool
    {
        if (!is_null($this->sprintScope) && (int) $sprint->id_sprint !== (int) $this->sprintScope) {
            return false;
        }

        if ($this->filtersAreEmpty()) {
            return true;
        }

        foreach ($sprint->epics as $epic) {
            if ($this->epicMatchesFilters($sprint, $epic)) {
                return true;
            }
        }

        foreach ($sprint->taches->where('id_epic', null) as $task) {
            if ($this->taskMatchesFilters($task)) {
                return true;
            }
        }

        return false;
    }

    public function epicMatchesFilters(Sprint $sprint, Epic $epic): bool
    {
        if ($this->filtersAreEmpty()) {
            return true;
        }

        foreach ($epic->taches as $task) {
            if ($task->id_sprint == $sprint->id_sprint && $this->taskMatchesFilters($task)) {
                return true;
            }
        }

        return false;
    }

    public function taskMatchesFilters(Tache $task): bool
    {
        $assignee = $this->appliedFilters['assignee'] ?? '';
        $status = $this->appliedFilters['status'] ?? '';
        $from = $this->appliedFilters['date_from'] ?? '';
        $to = $this->appliedFilters['date_to'] ?? '';

        if ($assignee !== '' && (string) $task->id_utilisateur !== (string) $assignee) {
            return false;
        }

        if ($status !== '' && (string) $task->status !== (string) $status) {
            return false;
        }

        if ($from !== '' && $task->start_date && $task->start_date < $from) {
            return false;
        }

        if ($to !== '' && $task->deadline && $task->deadline > $to) {
            return false;
        }

        $term = trim($this->taskSearch);
        if ($term !== '') {
            $title  = mb_strtolower((string) ($task->titre ?? ''));
            $needle = mb_strtolower($term);

            if ($title === '' || strpos($title, $needle) === false) {
                return false;
            }
        }

        return true;
    }

    public function render()
    {
        $assigneeOptions = Utilisateur::pluck('nom', 'id_utilisateur')->toArray();

        return view('livewire.project-board', [
            'projet' => $this->projet,
            'assigneeOptions' => $assigneeOptions,
        ]);
    }
}
