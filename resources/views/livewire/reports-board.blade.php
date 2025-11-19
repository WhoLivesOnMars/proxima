@php
    $totalTasks = $stats['to_do'] + $stats['in_progress'] + $stats['done'];
    $den = max($totalTasks, 1);

    $shareTodo = $totalTasks ? $stats['to_do'] / $den * 100 : 0;
    $shareInProgress = $totalTasks ? $stats['in_progress'] / $den * 100 : 0;
    $shareDone = 100 - $shareTodo - $shareInProgress;

    $pctTodo = round($shareTodo);
    $pctInProgress = round($shareInProgress);
    $pctDone = round($shareDone);

    $maxCount  = max($stats['to_do'], $stats['in_progress'], $stats['done'], 1);
    $barMaxPx  = 160;
    $barMinPx  = 16;

    $barHeightsPx = [
        'todo' => $stats['to_do'] ? ($stats['to_do'] / $maxCount) * $barMaxPx + $barMinPx : 0,
        'in_progress' => $stats['in_progress'] ? ($stats['in_progress'] / $maxCount) * $barMaxPx + $barMinPx : 0,
        'done' => $stats['done'] ? ($stats['done'] / $maxCount) * $barMaxPx + $barMinPx : 0,
    ];
@endphp

<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <div class="relative" x-data="{ open:false }">
                <button type="button"
                        class="inline-flex items-center gap-1 text-lg font-semibold text-slate-900 focus:outline-none"
                        @click="open = !open">
                    {{ $currentProject?->nom ?? 'Select project' }}
                    <svg class="w-4 h-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open"
                     x-transition
                     @click.outside="open = false"
                     class="absolute mt-2 w-56 bg-white rounded-md shadow border z-30">
                    @foreach($projects as $proj)
                        <button type="button"
                                wire:click="$set('currentProjectId', {{ $proj->id_projet }})"
                                @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100
                                {{ $currentProject && $currentProject->id_projet === $proj->id_projet ? 'font-semibold' : '' }}">
                            {{ $proj->nom }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="text-sm text-slate-500"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="text-center text-sm font-semibold text-slate-900 mb-20">
                Project progress statistics
            </div>

            <div class="mt-4 h-56 px-10 pb-4 flex items-end justify-between">
                <div class="flex flex-col items-center flex-1">
                    @if($barHeightsPx['todo'] > 0)
                        <div class="w-24 rounded-xl shadow-md shadow-slate-300/70 bg-[#EA4E98]"
                             style="height: {{ $barHeightsPx['todo'] }}px;">
                        </div>
                    @endif
                    <div class="mt-3 text-[11px] text-slate-600">
                        To do ({{ $stats['to_do'] }})
                    </div>
                </div>

                <div class="flex flex-col items-center flex-1">
                    @if($barHeightsPx['in_progress'] > 0)
                        <div class="w-24 rounded-xl shadow-md shadow-slate-300/70 bg-[#3687BE]"
                             style="height: {{ $barHeightsPx['in_progress'] }}px;">
                        </div>
                    @endif
                    <div class="mt-3 text-[11px] text-slate-600">
                        In progress ({{ $stats['in_progress'] }})
                    </div>
                </div>

                <div class="flex flex-col items-center flex-1">
                    @if($barHeightsPx['done'] > 0)
                        <div class="w-24 rounded-xl shadow-md shadow-slate-300/70 bg-[#1F9D8F]"
                             style="height: {{ $barHeightsPx['done'] }}px;">
                        </div>
                    @endif
                    <div class="mt-3 text-[11px] text-slate-600">
                        Done ({{ $stats['done'] }})
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <div class="text-center text-sm font-semibold text-slate-900 mb-20">
                Pie chart by status
            </div>

            <div class="mt-4 flex flex-col md:flex-row items-center justify-center gap-16">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <div class="w-40 h-40 rounded-full"
                        style="
                            background:
                                conic-gradient(
                                    #EA4E98 0 {{ $shareTodo }}%,
                                    #3687BE {{ $shareTodo }}% {{ $shareTodo + $shareInProgress }}%,
                                    #1F9D8F {{ $shareTodo + $shareInProgress }}% 100%
                                );
                        ">
                    </div>

                    <div class="absolute w-24 h-24 bg-white rounded-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-xs text-slate-500">Total</div>
                            <div class="text-lg font-semibold text-slate-900">
                                {{ $totalTasks }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-[#EA4E98] shadow-sm shadow-slate-300/70"></span>
                        <span class="text-slate-600">To do ({{ $pctTodo }}%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-[#3687BE] shadow-sm shadow-slate-300/70"></span>
                        <span class="text-slate-600">In progress ({{ $pctInProgress }}%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-[#1F9D8F] shadow-sm shadow-slate-300/70"></span>
                        <span class="text-slate-600">Done ({{ $pctDone }}%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">To do</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['to_do'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">In progress</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['in_progress'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">Done</div>
            <div class="text-lg font-semibold text-slate-900">
                {{ $stats['done'] }}
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3">
            <div class="text-slate-500 mb-1">Overdue</div>
            <div class="text-lg font-semibold text-rose-600">
                {{ $stats['overdue'] }}
            </div>
        </div>
    </div>
</div>
