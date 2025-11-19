<div
    x-data="{
      draggedId:null, openMenuId:null,
      menuTop:0, menuLeft:0,
      showMoveSub:false, showAssignSub:false,
      currentAssignee:'—',

      openFilter:false,

      modalOpen: @entangle('modalOpen'),
      modalTitre: @entangle('modalTitre'),
      modalDescription: @entangle('modalDescription'),
      modalDeadline: @entangle('modalDeadline'),
      modalAssigneeId: @entangle('modalAssigneeId'),
      modalStartDate: @entangle('modalStartDate'),
      modalEpicId: @entangle('modalEpicId'),

      openMenu(taskId, el){
        this.openMenuId = taskId;
        this.showMoveSub = false; this.showAssignSub = false;

        const rect = el.getBoundingClientRect();
        this.menuTop  = rect.top  + window.scrollY;
        this.menuLeft = rect.right + 12 + window.scrollX;

        this.currentAssignee = el.dataset.assignee || '—';

        this.$nextTick(()=>{ document.activeElement?.blur(); });
      },

      closeMenu(){
        this.openMenuId = null;
        this.showMoveSub = false;
        this.showAssignSub = false;
        this.menuTop = 0; this.menuLeft = 0;
      },
    }"
    x-init="
        Livewire.on('kanban-error', e => {
            alert(e.message);
        });
        Livewire.on('kanban-info', e => {
            console.log(e.message);
        });
    "
    @keydown.window.escape="closeMenu()"
    class="space-y-6"
>
    <div class="flex items-end justify-between">
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

            <div class="text-sm text-slate-500">
                {{ $currentSprint?->nom ?? 'No sprint' }}
            </div>
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

    <div class="grid grid-cols-4 gap-8 items-start w-full">
        @php
            $columns = [
                'todo' => ['label' => 'To do', 'color' => '#ea4e98'],
                'in_progress' => ['label' => 'In progress', 'color' => '#3687be'],
                'done' => ['label' => 'Done', 'color' => '#1f9d8f'],
                'for_approval' => ['label' => 'For approval', 'color' => '#969ba1'],
            ];
        @endphp

        @foreach($columns as $code => $meta)
            <div class="min-w-0">
                <div class="rounded-t-xl px-4 py-1.5 flex items-center justify-between"
                     style="background-color: {{ $meta['color'] }};">
                    <div class="text-white font-semibold text-sm">
                        {{ $meta['label'] }}
                    </div>
                    <div class="flex items-center gap-1 text-xs text-white/90">
                        <span>Tasks</span>
                        <span class="inline-flex items-center justify-center min-w-6 h-6 rounded-full bg-white/20 text-white text-xs font-semibold">
                            {{ count($tasksByStatus[$code] ?? []) }}
                        </span>
                    </div>
                </div>

                <div
                    class="pt-3 space-y-3"
                    @dragover.prevent
                    @drop.prevent="
                        if (draggedId) {
                            $wire.moveTaskWithRules(draggedId, '{{ $code }}');
                            draggedId = null;
                        }
                    "
                >
                    @if($code === 'todo')
                        <div class="group w-full bg-white rounded-lg shadow-sm border border-slate-100 px-3 flex items-center justify-between cursor-text hover:shadow-md transition">
                            <input
                                type="text"
                                placeholder="Create"
                                class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 appearance-none text-sm text-slate-700"
                                x-on:keydown.enter.prevent="
                                    if ($event.target.value.trim() !== '') {
                                        $wire.set('newTitle', $event.target.value);
                                        $wire.storeNewTask('todo');
                                        $event.target.value = '';
                                    }
                                "
                            >
                            <button
                                type="button"
                                class="ml-2 text-slate-400 text-lg leading-none opacity-0 group-hover:opacity-100 transition focus:outline-none"
                                x-on:click="
                                    const inp = $el.previousElementSibling;
                                    if (inp.value.trim() !== '') {
                                        $wire.set('newTitle', inp.value);
                                        $wire.storeNewTask('todo');
                                        inp.value = '';
                                    }
                                "
                            >+</button>
                        </div>
                    @endif

                    @foreach($tasksByStatus[$code] ?? [] as $task)
                        <div
                            draggable="true"
                            @dragstart="draggedId = {{ $task->id_tache }}"
                            @dragend="draggedId = null"
                            class="w-full bg-white rounded-lg shadow-sm px-3 py-2.5 space-y-2 border border-slate-100 hover:shadow-md transition"
                            :class="openMenuId === {{ $task->id_tache }} ? 'relative z-40' : ''"
                            data-assignee="{{ $task->assignee ? e(trim(($task->assignee->prenom ?? '').' '.($task->assignee->nom ?? ''))) : '—' }}"
                        >
                            <div class="flex items-start justify-between">
                                <div class="h-5 flex items-center w-full text-[11px] text-slate-400 leading-none">
                                    {{ $task->epic?->nom ?? '—' }}
                                </div>
                                @if(!$viaToken)
                                    <button
                                        type="button"
                                        class="p-1 rounded hover:bg-slate-100 focus:outline-none"
                                        @click.stop="openMenu({{ $task->id_tache }}, $event.target.closest('div[draggable]'))"
                                    >
                                        <x-heroicon-o-ellipsis-horizontal class="w-5 h-5 text-slate-400" />
                                    </button>
                                @endif
                            </div>

                            <input
                                type="text"
                                value="{{ $task->titre }}"
                                class="w-full bg-transparent border-none outline-none focus:outline-none focus:ring-0 p-0 text-sm font-semibold text-slate-900"
                                x-on:change="$wire.updateTitle({{ $task->id_tache }}, $event.target.value)"
                                @unless($canEditCards) disabled @endunless
                            />

                            @if(($task->description && trim($task->description) !== '') || $task->attachment_path)
                                <div class="flex items-center gap-1 text-[11px] text-slate-500 mt-1">
                                    @if($task->description && trim($task->description) !== '')
                                        <x-heroicon-o-document-text class="w-3.5 h-3.5" />
                                    @endif

                                    @if($task->attachment_path)
                                        <x-heroicon-o-paper-clip class="w-3.5 h-3.5" />
                                    @endif
                                </div>
                            @endif

                            <div class="flex items-center justify-between mt-1">
                                <label class="inline-flex items-center gap-1 text-[11px] text-slate-500">
                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                    <input
                                        type="date"
                                        value="{{ $task->deadline ? $task->deadline->format('Y-m-d') : '' }}"
                                        class="border-none bg-transparent outline-none focus:outline-none focus:ring-0 p-0 text-[11px]"
                                        x-on:change="$wire.updateDeadline({{ $task->id_tache }}, $event.target.value)"
                                        @unless($canEditCards) disabled @endunless
                                    >
                                </label>

                                @if($task->assignee)
                                    <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[11px] font-semibold text-slate-600">
                                        {{ $this->initials($task->assignee) }}
                                    </div>
                                @else
                                    <div class="w-7 h-7"></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div
        x-show="openMenuId !== null"
        x-cloak
    >
        <div class="fixed inset-0 bg-slate-900/30 z-30" @click="closeMenu()"></div>

        <div class="absolute z-50 flex flex-col gap-1"
            :style="`top:${menuTop}px; left:${menuLeft}px;`"
            @click.outside="closeMenu()"
            tabindex="-1"
        >
            <button
                type="button"
                class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-md text-xs text-slate-700 hover:bg-slate-200 transition"
                @click="$wire.openCard(openMenuId); closeMenu();"
            >
                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                <span>Open card</span>
            </button>

            @if($canEditCards)
                <div class="relative">
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-md text-xs text-slate-700 hover:bg-slate-200 transition"
                        @click.stop="showAssignSub = !showAssignSub; showMoveSub = false;"
                    >
                        <x-heroicon-o-user-group class="w-4 h-4" />
                        <span>Edit members</span>
                    </button>

                    <div
                        x-show="showAssignSub"
                        x-transition
                        class="absolute left-full top-0 ml-2 bg-white shadow-lg rounded-md border p-2 min-w-[180px] z-50"
                    >
                        <div class="text-[11px] text-slate-400 mb-1">Current assignee</div>
                        <div class="inline-flex items-center px-2 py-1 rounded bg-emerald-600 text-white text-xs font-medium mb-2">
                            <span x-text="currentAssignee"></span>
                        </div>

                        <div class="text-[11px] text-slate-400 mb-1">Assign to</div>
                        <div class="flex flex-col gap-1">
                            @foreach($assigneeOptions as $uid => $uname)
                                <button
                                    type="button"
                                    class="text-xs text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded text-left"
                                    @click="
                                      $wire.assignTo(openMenuId, {{ (int)$uid }});
                                      currentAssignee = '{{ $uname }}';
                                      closeMenu();
                                    "
                                >{{ $uname }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            <div class="relative">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-md text-xs text-slate-700 hover:bg-slate-200 transition"
                    @click.stop="showMoveSub = !showMoveSub; showAssignSub = false;"
                >
                    <x-heroicon-o-arrow-right-circle class="w-4 h-4" />
                    <span>Move to</span>
                </button>

                <div
                    x-show="showMoveSub"
                    x-transition
                    class="absolute left-full top-0 ml-2 bg-white shadow-lg rounded-md border px-2 py-2 flex flex-col gap-1 min-w-[120px] z-50"
                >
                    @foreach($columns as $code => $meta)
                        <button
                            type="button"
                            class="text-xs text-slate-700 bg-slate-100/70 hover:bg-slate-200 px-2 py-1 rounded"
                            @click="$wire.moveTaskWithRules(openMenuId, '{{ $code }}'); closeMenu();"
                        >
                            {{ $meta['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button
                type="button"
                class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-md text-xs text-slate-700 hover:bg-slate-200 transition"
                @click="$wire.copyTask(openMenuId); closeMenu();"
            >
                <x-heroicon-o-document-duplicate class="w-4 h-4" />
                <span>Copy card</span>
            </button>

            @if($canEditCards)
                <button
                    type="button"
                    class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-md text-xs text-rose-600 hover:bg-rose-100 transition"
                    @click="$wire.deleteTask(openMenuId); closeMenu();"
                >
                    <x-heroicon-o-trash class="w-4 h-4" />
                    <span>Delete card</span>
                </button>
            @endif
        </div>
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
                  <label class="block text-xs text-slate-500 mb-1">Title</label>
                  <input type="text" class="w-full rounded border-slate-300" x-model="modalTitre">
              </div>

              <div>
                  <label class="block text-xs text-slate-500 mb-1">Description</label>
                  <textarea rows="4" class="w-full rounded border-slate-300" x-model="modalDescription"></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs text-slate-500 mb-1">Start date</label>
                  <input type="date" class="w-full rounded border-slate-300" x-model="modalStartDate">
                </div>

                <div>
                  <label class="block text-xs text-slate-500 mb-1">Deadline</label>
                  <input type="date" class="w-full rounded border-slate-300" x-model="modalDeadline">
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                  <label class="block text-xs text-slate-500 mb-1">Epic</label>
                  <select class="w-full rounded border-slate-300" x-model="modalEpicId">
                    <option value="">—</option>
                    @foreach($epicOptions as $eid => $ename)
                      <option value="{{ $eid }}">{{ $ename }}</option>
                    @endforeach
                  </select>
                </div>

                <div>
                  <label class="block text-xs text-slate-500 mb-1">Assignee</label>
                  <select class="w-full rounded border-slate-300" x-model="modalAssigneeId">
                    <option value="">—</option>
                    @foreach($assigneeOptions as $uid => $uname)
                      <option value="{{ $uid }}">{{ $uname }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="mt-4">
                <label class="block text-xs text-slate-500 mb-1">Attachment</label>

                <input type="file"
                       class="w-full text-sm"
                       wire:model="modalAttachment">

                @error('modalAttachment')
                  <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror

                @if($modalAttachmentPath)
                    @php $fileName = basename($modalAttachmentPath); @endphp

                    <div class="mt-2 flex items-center justify-between text-xs">
                      <a href="{{ asset('storage/'.$modalAttachmentPath) }}"
                         class="text-emerald-700 underline break-all"
                         target="_blank">
                        {{ $fileName }}
                      </a>

                      <button type="button"
                              wire:click="deleteAttachment"
                              class="text-slate-400 hover:text-rose-600 transition"
                              title="Remove file">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-4 h-4"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor"
                             stroke-width="2">
                          <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                @endif
              </div>
            </div>

            <div class="mt-5 flex items-center justify-end gap-2">
                <button class="px-3 py-1.5 rounded border text-slate-600" @click="modalOpen=false">Cancel</button>
                <button type="button" class="px-3 py-1.5 rounded bg-emerald-600 text-white" wire:click="saveCard">
                    Save
                </button>
            </div>
          </div>
        </div>
    </template>
</div>
