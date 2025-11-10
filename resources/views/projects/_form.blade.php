@csrf
<div class="space-y-4">
  <div>
    <label class="block text-sm mb-1">Name</label>
    <input name="nom" value="{{ old('nom', $projet->nom) }}" required
           class="w-full border rounded-md px-3 py-2 @error('nom') border-red-500 @enderror">
    @error('nom') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
  </div>

  <div>
    <label class="block text-sm mb-1">Description</label>
    <textarea name="description" rows="4"
              class="w-full border rounded-md px-3 py-2">{{ old('description', $projet->description) }}</textarea>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full border rounded-md px-3 py-2">
        @foreach (['active','completed'] as $s)
          <option value="{{ $s }}" @selected(old('status',$projet->status)===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm mb-1">Visibility</label>
      <select name="visibility" class="w-full border rounded-md px-3 py-2">
        @foreach (['private','shared','public'] as $v)
          <option value="{{ $v }}" @selected(old('visibility',$projet->visibility)===$v)>{{ ucfirst($v) }}</option>
        @endforeach
      </select>
      @if ($projet->share_token)
        <p class="text-xs text-gray-500 mt-1">Share token: {{ $projet->share_token }}</p>
      @endif
    </div>
  </div>
</div>

<div class="mt-6 flex gap-2">
  <a href="{{ route('projects.index') }}" class="px-4 py-2 rounded border">Cancel</a>
  <button class="px-4 py-2 rounded bg-primary-500 hover:bg-primary-700 text-white">
    {{ $submit ?? 'Save' }}
  </button>
</div>
