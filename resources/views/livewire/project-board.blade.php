<div
    x-data="{
        openFilter: false,
        draggedEpic: null,
        draggedTask: null,
    }"
    @inline-error.window="alert($event.detail.message)"
>
    <div class="mb-6 flex items-center gap-3">
        @if($canManageStructure)
            <a href="{{ route('projects.sprints.create', ['projet' => $projet]) }}"
                class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
                + New Sprint
            </a>
            <a href="{{ route('projects.epics.create', ['projet' => $projet]) }}"
                class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
                + New Epic
            </a>
        @endif

        @if($canCreateTask)
            <a href="{{ route('projects.tasks.create', ['projet' => $projet]) }}"
                class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
                + New Task
            </a>
        @endif
    </div>

    <div class="mb-3 flex items-start justify-between">
        @php
            $currentSprint = $sprintScope
                ? $projet->sprints->firstWhere('id_sprint', $sprintScope)
                : null;
        @endphp

        <div class="relative" x-data="{openScope:false}">
            <button type="button"
                    class="inline-flex items-center gap-1 font-bold text-xl uppercase tracking-wide"
                    @click="openScope = !openScope">
                {{ $currentSprint ? $currentSprint->nom : 'ALL' }}
                <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5h-9.5Z"/></svg>
            </button>

            <div x-show="openScope"
                 x-transition
                 @click.outside="openScope = false"
                 class="absolute z-50 mt-2 w-48 rounded-md bg-white shadow border">
                <button type="button"
                        class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $sprintScope === null ? 'font-semibold' : '' }}"
                        @click="openScope = false"
                        wire:click="setSprintScope(null)">
                    All
                </button>
                @foreach($projet->sprints as $sp)
                    <button type="button"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 {{ $sprintScope === $sp->id_sprint ? 'font-semibold' : '' }}"
                            @click="openScope = false"
                            wire:click="setSprintScope({{ $sp->id_sprint }})">
                        {{ $sp->nom }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative flex-none w-64 sm:w-72">
                <input
                    type="search"
                    placeholder="Search tasks"
                    wire:model.defer="taskSearch"
                    wire:keydown.enter="applySearch"
                    class="w-full rounded-2xl border border-primary-300 bg-white pe-9 h-10 text-sm"
                />
                <x-heroicon-o-magnifying-glass
                    class="w-5 h-5 absolute right-2 top-1/2 -translate-y-1/2 text-primary-500"
                />
            </div>

            <div class="relative">
                <button type="button"
                        class="inline-flex items-center gap-2 text-secondary-900"
                        @click="openFilter = !openFilter">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                    <span>Filter</span>
                </button>

                <div x-show="openFilter"
                    x-transition
                    @click.outside="openFilter = false"
                    class="absolute right-0 mt-2 w-[28rem] bg-white rounded-md shadow border p-4 z-50">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Assignee</label>
                            <select wire:model.defer="filters.assignee" class="w-full h-10 px-3 rounded-md border text-sm">
                                <option value="">Any</option>
                                @foreach($assigneeOptions as $uid => $uname)
                                    <option value="{{ $uid }}">{{ $uname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select wire:model.defer="filters.status" class="w-full h-10 px-3 rounded-md border text-sm">
                                <option value="">Any</option>
                                <option value="todo">To do</option>
                                <option value="in_progress">In progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Start from</label>
                            <input type="date" wire:model.defer="filters.date_from" class="w-full h-10 px-3 rounded-md border text-sm">
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Deadline to</label>
                            <input type="date" wire:model.defer="filters.date_to" class="w-full h-10 px-3 rounded-md border text-sm">
                        </div>
                    </div>
                    <div class="pt-3 flex items-center gap-2">
                        <button type="button"
                                class="px-4 py-2 rounded text-sm border"
                                wire:click="resetFilters"
                                @click="openFilter = false">
                            Reset
                        </button>
                        <button type="button"
                                class="px-4 py-2 rounded text-sm bg-primary-500 hover:bg-primary-700 text-white"
                                wire:click="applyFilters"
                                @click="openFilter = false">
                            Apply
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto rounded-md">
        <table class="w-full text-sm border-separate border-spacing-0">
            <thead class="bg-secondary-100 text-secondary-900">
            <tr>
                <th class="w-10"></th>
                <th class="py-2 px-3 text-left font-semibold underline border-r border-secondary-200">Title</th>
                <th class="py-2 px-3 text-center font-semibold underline border-r border-secondary-200">Start date</th>
                <th class="py-2 px-3 text-center font-semibold underline border-r border-secondary-200">End date</th>
                <th class="py-2 px-3 text-center font-semibold underline border-r border-secondary-200">Assignee</th>
                <th class="py-2 px-3 text-center font-semibold underline border-r border-secondary-200">Status</th>
                <th class="w-10"></th>
            </tr>
            </thead>

            <tbody
                wire:key="body-{{ $sprintScope ?? 'all' }}-{{ md5(json_encode($appliedFilters)) }}-{{ md5($taskSearch) }}"
                x-data="{ spOpen: {}, epOpen: {} }"
            >
            @forelse($projet->sprints as $sprint)
                @if(!$this->sprintMatchesFilters($sprint))
                    @continue
                @endif

                @php
                    $sprintStart = $sprint->start_date ? \Carbon\Carbon::parse($sprint->start_date) : null;
                    $raw = (int) $sprint->duree;
                    $durationDays = ($raw > 0 && $raw <= 6) ? $raw * 7 : $raw;
                    $durationDays = $durationDays ?: 1;
                    $sprintEnd = $sprintStart ? $sprintStart->copy()->addDays($durationDays - 1) : null;
                @endphp

                {{-- SPRINTS --}}
                <tr wire:key="sprint-{{ $sprint->id_sprint }}" class="bg-white"
                    x-init="spOpen[{{ $sprint->id_sprint }}] = true"
                    @dragover.prevent
                    @drop.prevent="
                        if (draggedEpic) {
                            $wire.moveEpicToSprint(draggedEpic, {{ $sprint->id_sprint }});
                            draggedEpic = null;
                        } else if (draggedTask) {
                            $wire.moveTaskToSprint(draggedTask, {{ $sprint->id_sprint }});
                            draggedTask = null;
                        }
                    ">
                    <td class="py-3 px-3 text-center border-t border-secondary-200">
                        <button @click="spOpen[{{ $sprint->id_sprint }}] = !spOpen[{{ $sprint->id_sprint }}]"
                                class="inline-flex w-5 h-5 items-center justify-center rounded border">
                            <span x-text="spOpen[{{ $sprint->id_sprint }}] ? '−' : '+'"></span>
                        </button>
                    </td>
                    <td class="py-3 px-3 font-medium border-t border-r border-secondary-200">
                        <span class="inline-flex items-center gap-2">
                            <span class="inline-block w-6 h-6 rounded-md bg-[#e2f0ff] text-xs grid place-items-center text-[#0f5fac]">S</span>
                            <input type="text" value="{{ $sprint->nom }}"
                                   x-on:change="$wire.updateField('sprint', {{ $sprint->id_sprint }}, 'nom', $event.target.value)"
                                   @unless($canManageStructure) disabled @endunless
                                   class="border-0 bg-transparent text-sm outline-none focus:outline-none focus:ring-0 focus:border-transparent">
                        </span>
                    </td>
                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        <input type="date" value="{{ $sprintStart?->format('Y-m-d') }}"
                               x-on:change="$wire.updateField('sprint', {{ $sprint->id_sprint }}, 'start_date', $event.target.value)"
                               @unless($canManageStructure) disabled @endunless
                               class="border-0 bg-transparent text-sm text-center outline-none focus:outline-none focus:ring-0 focus:border-transparent">
                    </td>
                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        <input type="date" value="{{ $sprintEnd?->format('Y-m-d') }}"
                               x-on:change="$wire.updateField('sprint', {{ $sprint->id_sprint }}, 'end_date', $event.target.value)"
                               @unless($canManageStructure) disabled @endunless
                               class="border-0 bg-transparent text-sm text-center outline-none focus:outline-none focus:ring-0 focus:border-transparent">
                    </td>
                    <td colspan="2" class="border-t border-r border-secondary-200 text-center text-gray-400"></td>
                    <td class="py-3 px-3 text-center border-t border-secondary-200">
                        @if($canDeleteItems)
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-secondary-100">
                                        <x-heroicon-o-ellipsis-vertical class="w-5 h-5"/>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <form method="POST" action="{{ route('sprints.destroy', $sprint) }}" onsubmit="return confirm('Delete this sprint?')">
                                        @csrf @method('DELETE')
                                        <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Delete</button>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        @endif
                    </td>
                </tr>

                {{-- EPICS --}}
                @foreach($sprint->epics as $epic)
                    @if(!$this->epicMatchesFilters($sprint, $epic)) @continue @endif
                    <tr wire:key="epic-{{ $epic->id_epic }}" class="bg-white"
                        x-show="spOpen[{{ $sprint->id_sprint }}]"
                        x-init="if (epOpen[{{ $epic->id_epic }}] === undefined) epOpen[{{ $epic->id_epic }}] = true"
                        draggable="true"
                        @dragstart="draggedEpic = {{ $epic->id_epic }}"
                        @dragend="draggedEpic = null"
                        @dragover.prevent
                        @drop.prevent="
                            if (draggedTask) {
                                $wire.moveTaskToEpic(draggedTask, {{ $epic->id_epic }});
                                draggedTask = null;
                            }
                        ">
                        <td class="py-3 px-3 text-center border-t border-secondary-200">
                            <button @click="epOpen[{{ $epic->id_epic }}] = !epOpen[{{ $epic->id_epic }}]"
                                    class="inline-flex w-5 h-5 items-center justify-center rounded border">
                                <span x-text="epOpen[{{ $epic->id_epic }}] ? '−' : '+'"></span>
                            </button>
                        </td>
                        <td class="py-3 px-3 border-t border-r border-secondary-200">
                            <span class="inline-flex items-center gap-2 pl-6">
                                <span class="inline-block w-6 h-6 rounded-md bg-[#fff2cc] text-xs grid place-items-center text-[#946200]">E</span>
                                <input type="text" value="{{ $epic->nom }}"
                                       x-on:change="$wire.updateField('epic', {{ $epic->id_epic }}, 'nom', $event.target.value)"
                                       @unless($canManageStructure) disabled @endunless
                                       class="border-0 bg-transparent text-sm outline-none focus:outline-none focus:ring-0 focus:border-transparent">
                            </span>
                        </td>
                        <td colspan="4" class="text-center border-t border-r border-secondary-200 text-gray-400"></td>
                        <td class="py-3 px-3 text-center border-t border-secondary-200">
                            @if($canDeleteItems)
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-secondary-100">
                                            <x-heroicon-o-ellipsis-vertical class="w-5 h-5"/>
                                        </button>
                                    </x-slot>
                                    <x-slot name="content">
                                        <form method="POST" action="{{ route('epics.destroy', $epic) }}" onsubmit="return confirm('Delete this epic?')">
                                            @csrf @method('DELETE')
                                            <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Delete</button>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            @endif
                        </td>
                    </tr>

                    {{-- TASKS OF EPIC --}}
                    @foreach(($epic->taches ?? collect()) as $task)
                        @if($task->id_sprint == $sprint->id_sprint && $this->taskMatchesFilters($task))
                            @include('projects.partials.task-row', [
                                'task' => $task,
                                'indent' => 12,
                                'sprint' => $sprint,
                                'epic' => $epic,
                                'assigneeOptions' => $assigneeOptions,
                                'isOwner' => $isOwner,
                                'canChangeStatus' => $canChangeStatus,
                                'canDelete' => $canDeleteItems,
                            ])
                        @endif
                    @endforeach
                @endforeach

                {{-- TASKS WITHOUT EPIC --}}
                @foreach($sprint->taches->where('id_epic', null) as $task)
                    @if($this->taskMatchesFilters($task))
                        @include('projects.partials.task-row', [
                            'task' => $task,
                            'indent' => 6,
                            'sprint' => $sprint,
                            'epic' => null,
                            'assigneeOptions' => $assigneeOptions,
                            'isOwner' => $isOwner,
                            'canChangeStatus' => $canChangeStatus,
                            'canDelete' => $canDeleteItems,
                        ])
                    @endif
                @endforeach

            @empty
                <tr><td colspan="7" class="py-10 px-3 text-center text-gray-500">No sprints yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
