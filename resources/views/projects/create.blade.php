<x-app-layout>
  <x-slot name="header"><h2 class="font-semibold text-2xl">New Project</h2></x-slot>

  <div class="max-w-3xl">
    <form action="{{ route('projects.store') }}" method="POST"
          class="bg-white shadow sm:rounded-lg p-6">
      @csrf
      @include('projects._form', ['projet' => $projet, 'submit' => 'Create'])
    </form>
  </div>
</x-app-layout>
