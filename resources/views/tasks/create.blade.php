<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">New Task</h2>
  </x-slot>

  <div
    class="rounded-md bg-white shadow ring-1 ring-secondary-200 p-5"
    x-data="{
      sprint: '{{ old('id_sprint') }}',
      epicsBySprint: @js($epicsBySprint),
      get epics() { return this.epicsBySprint[this.sprint] ?? []; }
    }"
  >
    <form method="POST" action="{{ route('projects.tasks.store', ['projet' => $project]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      @csrf

      <div class="md:col-span-2">
        <label class="block text-sm mb-1">Title</label>
        <input type="text" name="titre" class="w-full h-10 px-3 rounded-md border text-sm" value="{{ old('titre') }}" required>
        @error('titre') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Sprint</label>
        <select name="id_sprint" x-model="sprint" class="w-full h-10 px-3 rounded-md border text-sm" required>
          <option value="">Select sprint…</option>
          @foreach($sprints as $s)
            <option value="{{ $s->id_sprint }}">{{ $s->nom }}</option>
          @endforeach
        </select>
        @error('id_sprint') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Epic (optional)</label>
        <select name="id_epic" class="w-full h-10 px-3 rounded-md border text-sm">
          <option value="">—</option>
          <template x-for="e in epics" :key="e.id">
            <option :value="e.id" x-text="e.nom"></option>
          </template>
        </select>
        @error('id_epic') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Start date</label>
        <input type="date" name="start_date" value="{{ old('start_date') }}"
            class="w-full h-10 px-3 rounded-md border text-sm">
         @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Deadline</label>
        <input type="date" name="deadline" value="{{ old('deadline') }}" class="w-full h-10 px-3 rounded-md border text-sm">
        @error('deadline') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Status</label>
        <select name="status" class="w-full h-10 px-3 rounded-md border text-sm">
          @foreach(['todo'=>'To do','in_progress'=>'In process','done'=>'Done'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('status','todo')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-2 flex items-center gap-2 pt-2">
        <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded border">Cancel</a>
        <button class="px-5 py-2 rounded bg-primary-500 hover:bg-primary-700 text-white">Create Task</button>
      </div>
    </form>
  </div>
</x-app-layout>
