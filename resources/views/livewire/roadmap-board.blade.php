<div
    x-data="{
        modalOpen: @entangle('modalOpen'),
    }"
    class="space-y-6"
>
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <div class="relative" x-data="{open:false}">
                <button type="button"
                        class="inline-flex items-center gap-1 font-bold text-xl uppercase tracking-wide"
                        @click="open = !open">
                    {{ $currentProject?->nom ?? 'Select project' }}
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path d="M5.25 7.5 10 12.25 14.75 7.5h-9.5Z"/></svg>
                </button>

                <div x-show="open"
                     x-transition
                     @click.outside="open = false"
                     class="absolute mt-2 w-56 bg-white rounded-md shadow border z-30">
                    @foreach($projects as $proj)
                        <button type="button"
                                wire:click="selectProject({{ $proj->id_projet }})"
                                @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 {{ $currentProject && $currentProject->id_projet === $proj->id_projet ? 'font-semibold' : '' }}">
                            {{ $proj->nom }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="button" class="flex items-center gap-2 text-sm text-slate-600">
            <x-heroicon-o-funnel class="w-4 h-4" />
            <span>Filter</span>
        </button>
    </div>

    <div class="w-full overflow-x-auto">
        @if($currentProject && count($sprints) && count($epics))
            @php
                $colCount = count($sprints);
            @endphp

            <div class="min-w-[900px] space-y-3">

                <div class="grid text-xs text-slate-500"
                     style="grid-template-columns: 220px repeat({{ $colCount }}, minmax(180px, 1fr));">
                    <div class="px-4 pb-3 border-b border-slate-300 flex items-end">
                        <span class="font-semibold uppercase tracking-wide text-[11px] text-slate-500">
                            Epics
                        </span>
                    </div>

                    @foreach($sprints as $sprint)
                        @php
                            $progress = $this->getSprintProgress($sprint->id_sprint);

                            $start = $sprint->start_date ? \Carbon\Carbon::parse($sprint->start_date) : null;
                            $end = $sprint->end_date ? \Carbon\Carbon::parse($sprint->end_date) : null;

                            $mid = null;
                            if ($start && $end) {
                                $days = $start->diffInDays($end);
                                $mid = $start->copy()->addDays(intval(floor($days / 2)));
                            }
                        @endphp

                        <div class="px-4 pb-3 border-b border-slate-300 border-l border-slate-100">
                            <div class="text-xs font-semibold text-slate-700">
                                {{ $sprint->nom }}
                            </div>

                            <div class="mt-2 flex items-center justify-between text-[11px] text-slate-500">
                                <span>Progress</span>
                                <span>{{ $progress }}%</span>
                            </div>

                            <div class="mt-1 w-full h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-emerald-500" style="width: {{ $progress }}%;"></div>
                            </div>

                            @if($start && $end)
                                <div class="mt-1 relative w-full text-[10px] text-slate-400">
                                    <span class="absolute left-0">
                                        {{ $start->format('d/m') }}
                                    </span>
                                    <span class="absolute right-0">
                                        {{ $end->format('d/m') }}
                                    </span>
                                </div>
                            @endif

                            <div class="mt-3 flex justify-center">
                                <div class="w-px h-5 bg-slate-400"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @foreach($epics as $epic)
                    @php
                        $epicBgColors = [
                            '#FFE4B8',
                            '#C6F2FF',
                            '#F6C6FF',
                            '#D7FFCB',
                            '#FFE1F0',
                        ];
                        $bgColor = $epicBgColors[$loop->index % count($epicBgColors)];
                        $epicProgress = $this->getEpicProgress($epic->id_epic);
                    @endphp

                    <div class="grid rounded-2xl bg-white shadow-sm"
                         style="grid-template-columns: 220px repeat({{ $colCount }}, minmax(180px, 1fr));">
                        <div class="px-4 py-4 rounded-l-2xl border-r border-slate-100 flex flex-col justify-between"
                             style="background-color: {{ $bgColor }};">
                            <div class="text-sm font-semibold text-slate-900">
                                {{ $epic->nom }}
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-[11px] text-slate-700">
                                    <span>Progress</span>
                                    <span>{{ $epicProgress }}%</span>
                                </div>
                                <div class="mt-1 w-full h-1.5 rounded-full bg-white/70 overflow-hidden">
                                    <div class="h-full bg-sky-600" style="width: {{ $epicProgress }}%;"></div>
                                </div>
                            </div>
                        </div>

                        @foreach($sprints as $sprint)
                            @php
                                $cellTasks = $grid[$epic->id_epic][$sprint->id_sprint] ?? [];
                                $rowCount = max(count($cellTasks), 1);
                            @endphp

                            <div class="px-4 py-4 border-t border-l border-slate-100 bg-white">
                                <div class="relative w-full" style="height: {{ $rowCount * 2.4 }}rem;">
                                    @foreach($cellTasks as $task)
                                        @php
                                            $segment = $this->getTaskSegment($task, $sprint);
                                            if ($segment['width'] <= 0) continue;

                                            $statusStyle = match($task->status) {
                                                'done' => 'background-color: rgba(31, 157, 143, 0.18); color:#1F9D8F;',
                                                'in_progress' => 'background-color: rgba(54, 135, 190, 0.18); color:#3687BE;',
                                                default => 'background-color: rgba(234, 78, 152, 0.18); color:#EA4E98;',
                                            };

                                            $top = $loop->index * 2.3;
                                        @endphp

                                        <button
                                            type="button"
                                            class="absolute inline-flex items-center justify-between rounded-full px-3 py-1.5 text-[11px] font-medium hover:shadow-sm transition"
                                            style="
                                                top: {{ $top }}rem;
                                                left: {{ $segment['offset'] }}%;
                                                width: {{ $segment['width'] }}%;
                                                {{ $statusStyle }}
                                            "
                                            wire:click="openCard({{ $task->id_tache }})"
                                            @click="modalOpen = true"
                                        >
                                            <span class="truncate">{{ $task->titre }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-sm text-slate-500">
                No data to display yet. Create a project with sprints, epics and tasks.
            </div>
        @endif
    </div>

    <template x-teleport="body">
        <div
          x-show="modalOpen"
          x-transition
          x-cloak
          class="fixed inset-0 z-[120] flex items-center justify-center"
          @keydown.window.escape="modalOpen=false"
        >
          <div class="absolute inset-0 bg-slate-900/40" @click="modalOpen=false"></div>

          <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl p-5 z-[130]">
            <div class="flex justify-end">
              <button class="p-1 rounded hover:bg-slate-100" @click="modalOpen=false">
                <x-heroicon-o-x-mark class="w-5 h-5 text-slate-500"/>
              </button>
            </div>

            <div class="space-y-4">
              <div>
                <div class="text-xs text-slate-500 mb-1">Title</div>
                <div class="text-sm font-semibold text-slate-900">
                    {{ $modalTitre }}
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 text-xs text-slate-600">
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Epic</div>
                    <div class="font-medium">{{ $modalEpicName ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Sprint</div>
                    <div class="font-medium">{{ $modalSprintName ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Start date</div>
                    <div>{{ $modalStartDate ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Deadline</div>
                    <div>{{ $modalDeadline ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Assignee</div>
                    <div>{{ $modalAssigneeName ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-[11px] text-slate-500 mb-1">Status</div>
                    <div>{{ $modalStatus ?? '—' }}</div>
                </div>
              </div>

              @if($modalDescription)
                  <div>
                    <div class="text-xs text-slate-500 mb-1">Description</div>
                    <div class="text-sm text-slate-700 whitespace-pre-line">
                        {{ $modalDescription }}
                    </div>
                  </div>
              @endif

              @if($modalAttachmentPath)
                  @php $fileName = basename($modalAttachmentPath); @endphp
                  <div>
                      <div class="text-xs text-slate-500 mb-1">Attachment</div>
                      <a href="{{ asset('storage/'.$modalAttachmentPath) }}"
                         target="_blank"
                         class="text-xs text-emerald-700 underline break-all">
                          {{ $fileName }}
                      </a>
                  </div>
              @endif
            </div>
          </div>
        </div>
    </template>
</div>
