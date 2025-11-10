<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-2xl">Edit Project</h2></x-slot>

  <div class="max-w-3xl">
    <form action="{{ route('projects.update', $projet) }}" method="POST"
          class="bg-white shadow sm:rounded-lg p-6">
      @csrf
      @method('PUT')
      @include('projects._form', ['projet' => $projet, 'submit' => 'Save'])
    </form>
  </div>
</x-app-layout>
