<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl text-gray-800">New Sprint</h2>

      <div class="flex items-center gap-2">
        @isset($project)
          <a href="{{ route('projects.show', $project) }}"
          class="px-4 py-2 rounded border">Back to project</a>
        @endisset
      </div>
    </div>
  </x-slot>

  @if ($errors->any())
    <div class="mb-4 rounded bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
      <div class="font-medium mb-1">Please fix the errors below.</div>
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div
  x-data="{
    start: '{{ old('start_date', now()->format('Y-m-d')) }}',
    weeks: {{ old('duree', 2) }},
    fmt(iso){ if(!iso) return '—'; const [y,m,d]=iso.split('-'); return `${d}/${m}/${y}` },
    get end(){
      if(!this.start || !this.weeks) return '';
      const d = new Date(this.start);
      if (isNaN(d)) return '';
      d.setDate(d.getDate() + Number(this.weeks) * 7);
      return d.toISOString().slice(0,10);
    }
  }"
  class="rounded-md bg-white shadow ring-1 ring-secondary-200 p-5"
>
  <form method="POST" action="{{ route('projects.sprints.store', ['projet' => $project]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @csrf

    <div>
      <label class="block text-sm mb-1">Name</label>
      <input type="text" name="nom" value="{{ old('nom') }}" class="w-full h-10 px-3 rounded-md border text-sm" placeholder="Sprint 1">
      @error('nom') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">Start date</label>
      <input type="date" name="start_date" x-model="start" class="w-full h-10 px-3 rounded-md border text-sm">
      @error('start_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">Duration (weeks)</label>
      <select name="duree" x-model.number="weeks" class="w-full h-10 px-3 rounded-md border text-sm">
        @foreach([1,2,3,4] as $w)
          <option value="{{ $w }}">{{ $w }}</option>
        @endforeach
      </select>
      @error('duree') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">End date</label>
      <div class="w-full h-10 px-3 rounded-md border text-sm bg-gray-50 grid items-center"><span x-text="fmt(end)"></span></div>
    </div>

    <div class="md:col-span-2 flex items-center gap-2 pt-2">
      <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded border">Cancel</a>
      <button class="px-5 py-2 rounded bg-primary-500 hover:bg-primary-700 text-white">Create Sprint</button>
    </div>
  </form>
</div>
</x-app-layout>
