<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">New Epic</h2>
  </x-slot>

  @if ($errors->any())
    <div class="mb-4 rounded bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
      <div class="font-medium mb-1">Please fix the errors below.</div>
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-md bg-white shadow ring-1 ring-secondary-200 p-5">
    <form method="POST" action="{{ route('projects.epics.store', ['projet' => $project]) }}" class="grid grid-cols-1 md:grid-cols-2 gap-5">
      @csrf
      <div>
        <label class="block text-sm mb-1">Name</label>
        <input type="text" name="nom" class="w-full h-10 px-3 rounded-md border text-sm" value="{{ old('nom') }}" required>
        @error('nom') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Sprint</label>
        <select name="id_sprint" class="w-full h-10 px-3 rounded-md border text-sm" required>
          <option value="">Select sprint…</option>
          @foreach($sprints as $s)
            <option value="{{ $s->id_sprint }}" @selected(old('id_sprint')==$s->id_sprint)>
              {{ $s->nom }}
            </option>
          @endforeach
        </select>
        @error('id_sprint') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="md:col-span-2 flex items-center gap-2 pt-2">
        <a href="{{ route('projects.show', $project) }}" class="px-4 py-2 rounded border">Cancel</a>
        <button class="px-5 py-2 rounded bg-primary-500 hover:bg-primary-700 text-white">Create Epic</button>
      </div>
    </form>
  </div>
</x-app-layout>
