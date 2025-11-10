@php
    $key = isset($epic) && $epic
        ? "task-{$task->id_tache}-s{$sprint->id_sprint}-e{$epic->id_epic}"
        : "task-{$task->id_tache}-s{$sprint->id_sprint}-e0";

    $showCondition = isset($epic) && $epic
        ? "spOpen[{$sprint->id_sprint}] && epOpen[{$epic->id_epic}]"
        : "spOpen[{$sprint->id_sprint}]";
@endphp

<tr wire:key="{{ $key }}" class="bg-white"
    x-show="{{ $showCondition }}"
    draggable="true"
    @dragstart="draggedTask = {{ $task->id_tache }}"
    @dragend="draggedTask = null">
    <td class="py-3 px-3 text-center border-t border-secondary-200"></td>
    <td class="py-3 px-3 border-t border-r border-secondary-200">
        <span class="inline-flex items-center gap-2 pl-{{ $indent }}">
            <span class="inline-block w-6 h-6 rounded-md bg-green-200 text-green-700 text-xs font-bold grid place-items-center shadow-sm">T</span>
            <input type="text" value="{{ $task->titre }}"
                   x-on:change="$wire.updateField('task', {{ $task->id_tache }}, 'titre', $event.target.value)"
                   class="border-0 bg-transparent text-sm outline-none focus:outline-none focus:ring-0 focus:border-transparent">
        </span>
    </td>
    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
        <input type="date" value="{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('Y-m-d') : '' }}"
               x-on:change="$wire.updateField('task', {{ $task->id_tache }}, 'start_date', $event.target.value)"
               class="border-0 bg-transparent text-sm text-center outline-none focus:outline-none focus:ring-0 focus:border-transparent">
    </td>
    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
        <input type="date" value="{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d') : '' }}"
               x-on:change="$wire.updateField('task', {{ $task->id_tache }}, 'deadline', $event.target.value)"
               class="border-0 bg-transparent text-sm text-center outline-none focus:outline-none focus:ring-0 focus:border-transparent">
    </td>
    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
        <select
            x-on:change="$wire.updateField('task', {{ $task->id_tache }}, 'id_utilisateur', $event.target.value)"
            class="border-0 bg-transparent text-sm outline-none focus:outline-none focus:ring-0 focus:border-transparent"
        >
            <option value=""></option>
            @foreach($assigneeOptions as $uid => $uname)
                <option value="{{ $uid }}" @selected($task->id_utilisateur == $uid)>{{ $uname }}</option>
            @endforeach
        </select>
    </td>
    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
        <select
            x-on:change="$wire.updateField('task', {{ $task->id_tache }}, 'status', $event.target.value)"
            class="border-0 bg-transparent text-sm outline-none focus:outline-none focus:ring-0 focus:border-transparent"
        >
            <option value="todo" @selected($task->status === 'todo')>To do</option>
            <option value="in_progress" @selected($task->status === 'in_progress')>In progress</option>
            <option value="done" @selected($task->status === 'done')>Done</option>
        </select>
    </td>
    <td class="py-3 px-3 text-center border-t border-secondary-200">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-secondary-100">
                    <x-heroicon-o-ellipsis-vertical class="w-5 h-5"/>
                </button>
            </x-slot>
            <x-slot name="content">
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100">Delete</button>
                </form>
            </x-slot>
        </x-dropdown>
    </td>
</tr>
