<x-app-layout>

    <div class="space-y-8">

        <div>
            <h1 class="text-2xl font-semibold text-dark">
                Hello, {{ auth()->user()->prenom ?? 'there' }}
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                Here is an overview of your workspace.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-xl p-5 border border-primary-200/50">
                <div class="text-sm text-gray-500">Projects</div>
                <div class="text-3xl font-semibold mt-2">
                    {{ \App\Models\Projet::count() }}
                </div>
            </div>

            <div class="bg-white shadow rounded-xl p-5 border border-primary-200/50">
                <div class="text-sm text-gray-500">Tasks</div>
                <div class="text-3xl font-semibold mt-2">
                    {{ \App\Models\Tache::count() }}
                </div>
            </div>

            <div class="bg-white shadow rounded-xl p-5 border border-primary-200/50">
                <div class="text-sm text-gray-500">Sprints</div>
                <div class="text-3xl font-semibold mt-2">
                    {{ \App\Models\Sprint::count() }}
                </div>
            </div>

            <div class="bg-white shadow rounded-xl p-5 border border-primary-200/50">
                <div class="text-sm text-gray-500">Completed tasks</div>
                <div class="text-3xl font-semibold mt-2">
                    {{ \App\Models\Tache::where('status', 'done')->count() }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border border-primary-200/50">
            <h2 class="text-lg font-semibold mb-4 text-dark">Quick actions</h2>

            <div class="flex flex-wrap gap-4">
                <a href="{{ route('projects.create') }}"
                   class="px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition">
                    + New project
                </a>

                <a href="{{ route('kanban.index') }}"
                   class="px-4 py-2 rounded-lg bg-primary-100 text-primary-700 border border-primary-300 hover:bg-primary-200 transition">
                    Kanban board
                </a>

                <a href="{{ route('roadmap.index') }}"
                   class="px-4 py-2 rounded-lg bg-primary-100 text-primary-700 border border-primary-300 hover:bg-primary-200 transition">
                    Roadmap
                </a>

                <a href="{{ route('reports.index') }}"
                   class="px-4 py-2 rounded-lg bg-primary-100 text-primary-700 border border-primary-300 hover:bg-primary-200 transition">
                    Reports
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
