<x-app-layout>
    <x-slot name="header">
        <x-page.actions
            :create-href="route('projects.create')"
            create-label="New Project"
            edit-base="/projects"
            store="selection"
        />
    </x-slot>

    <div class="mb-3 flex items-start justify-between">
        <x-scope.dropdown
            :scope="$scope ?? 'all'"
            route-name="projects.index"
        />

        <div class="flex items-center gap-3">
            <form method="GET" action="{{ route('projects.index') }}" class="relative flex-none w-64 sm:w-72">
                @foreach (['scope','status','visibility','created_from','created_to'] as $keep)
                    @if (request()->filled($keep))
                        <input type="hidden" name="{{ $keep }}" value="{{ request($keep) }}">
                    @endif
                @endforeach

                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search projects"
                    class="w-full rounded-2xl border border-primary-300 bg-white pe-9 h-10 text-sm"
                />
                <x-heroicon-o-magnifying-glass
                    class="w-5 h-5 absolute right-2 top-1/2 -translate-y-1/2 text-primary-500"
                />
            </form>

            <x-filter.panel :action="route('projects.index')" :persist="['scope']" width="w-[28rem]">
                <div>
                    <label class="block text-sm mb-1">Status</label>
                    <select name="status" class="w-full h-10 px-3 rounded-md border text-sm">
                        <option value="">Any</option>
                        @foreach (['active','completed'] as $s)
                            <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Visibility</label>
                    <select name="visibility" class="w-full h-10 px-3 rounded-md border text-sm">
                        <option value="">Any</option>
                        @foreach (['private','shared'] as $v)
                            <option value="{{ $v }}" @selected(request('visibility')===$v)>{{ ucfirst($v) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Created from</label>
                    <input type="date" name="created_from" value="{{ request('created_from') }}"
                        class="w-full h-10 px-3 rounded-md border text-sm">
                </div>

                <div>
                    <label class="block text-sm mb-1">Created to</label>
                    <input type="date" name="created_to" value="{{ request('created_to') }}"
                        class="w-full h-10 px-3 rounded-md border text-sm">
                </div>
            </x-filter.panel>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-4 rounded bg-primary-500/10 border border-primary-500/20 px-4 py-2">
            {{ session('ok') }}
        </div>
    @endif

    @if (session('members_warning'))
        <div class="mb-2 rounded bg-yellow-100 border border-yellow-300 px-4 py-2 text-sm text-yellow-900">
            {{ session('members_warning') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-md">
        <table class="w-full text-sm border-separate border-spacing-0">
            <thead class="bg-secondary-100 text-secondary-900">
            <tr>
                <th class="w-10"></th>
                <th class="py-2 px-3 text-center font-semibold border-r border-secondary-200">Project</th>
                <th class="py-2 px-3 text-center font-semibold border-r border-secondary-200">Status</th>
                <th class="py-2 px-3 text-center font-semibold border-r border-secondary-200">Start date</th>
                <th class="py-2 px-3 text-center font-semibold border-r border-secondary-200">Current sprint</th>
                <th class="py-2 px-3 text-center font-semibold border-r border-secondary-200">Visibility</th>
            </tr>
            </thead>

            <tbody>
            @forelse ($projets as $projet)
                <tr class="bg-white">
                    <td class="py-3 px-3 text-center border-t border-secondary-200">
                        <label class="inline-flex items-center justify-center cursor-pointer select-none">
                            <input
                                type="checkbox"
                                class="sr-only peer"
                                :checked="$store.selection.id === @js($projet->id_projet)"
                                @change="$store.selection.id = $event.target.checked ? @js($projet->id_projet) : null"
                            >
                            <span class="block w-3.5 h-3.5 rounded-sm border border-gray-300 bg-gray-100
                                    peer-checked:bg-secondary-700 peer-checked:border-secondary-700"></span>
                        </label>
                    </td>

                    <td class="py-3 px-3 text-left font-medium border-t border-r border-secondary-200">
                        <a href="{{ route('projects.show', $projet) }}" class="underline hover:no-underline">
                            {{ $projet->nom }}
                        </a>
                    </td>

                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        <span class="inline-flex rounded px-2 py-0.5 text-xs
                            {{ $projet->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($projet->status) }}
                        </span>
                    </td>

                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        {{ $projet->firstSprint?->start_date?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        @php
                            $cs = $projet->currentSprint;
                        @endphp
                        @if ($cs)
                            @php
                                $start = $cs->start_date ? \Carbon\Carbon::parse($cs->start_date) : null;
                                $raw = (int) $cs->duree;
                                $days = ($raw > 0 && $raw <= 6) ? $raw * 7 : $raw;
                                $days = $days ?: 1;
                                $end = $start ? $start->copy()->addDays($days - 1) : null;
                            @endphp
                            <span class="font-medium">
                                {{ $cs->nom }}
                            </span>
                            <span class="block text-xs text-gray-500">
                                {{ $start?->format('d/m') }} – {{ $end?->format('d/m') }}
                            </span>
                        @else
                            —
                        @endif
                    </td>

                    <td class="py-3 px-3 text-center border-t border-r border-secondary-200">
                        <span class="inline-flex rounded px-2 py-0.5 text-xs bg-primary-100 text-secondary-900">
                            {{ ucfirst($projet->visibility) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-10 px-3 text-center text-gray-500">No projects yet</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $projets->appends(request()->query())->links() }}</div>
</x-app-layout>
