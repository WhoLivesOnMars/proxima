@php
    $collapsed = '$store.sidebar.collapsed';
@endphp

<nav class="h-full bg-secondary-700 border-r flex flex-col select-none relative">
    <button type="button"
        class="w-full h-14 flex items-center px-7 hover:bg-secondary-500"
        @click="$store.sidebar.toggle()">
        <template x-if="{{ $collapsed }}">
            <x-heroicon-o-bars-3 class="w-7 h-7 text-light"/>
        </template>
        <template x-if="!{{ $collapsed }}">
            <x-heroicon-o-x-mark class="w-7 h-7 text-light"/>
        </template>
    </button>

    <ul class="flex-1 space-y-1">
        @php
            $items = [
                ['route' => 'projects.index', 'icon' => 'folder', 'label' => 'Projects'],
                // ['route' => 'tasks.index', 'icon' => 'check-badge', 'label' => 'Tasks'],
                ['route' => 'kanban.index', 'icon' => 'rectangle-group', 'label' => 'Kanban'],
                ['route' => 'roadmap.index', 'icon' => 'map', 'label' => 'Roadmap'],
                ['route' => 'reports.index', 'icon' => 'chart-bar', 'label' => 'Reports'],
            ];
        @endphp

        @foreach ($items as $it)
            <li>
                <a href="{{ route($it['route']) }}"
                    class="group flex gap-2 items-center px-5 py-3 text-base hover:bg-secondary-500">
                    <span class="inline-flex w-10 justify-center shrink-0">
                        @php $icon = 'heroicon-o-'.$it['icon']; @endphp
                        <x-dynamic-component :component="$icon" class="w-7 h-7 text-primary-300"/>
                    </span>

                    <span x-cloak
                        class="text-light overflow-hidden whitespace-nowrap transition-all duration-200 ease-in-out"
                        :class="{{ $collapsed }}
                                    ? 'opacity-0 translate-x-2 w-0'
                                    : 'opacity-100 translate-x-0 w-auto'">
                        {{ $it['label'] }}
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="px-5 py-4 flex items-center justify-between gap-2">
        @php
            $u = Auth::user();
            $first = $u->prenom ?? $u->first_name ?? '';
            $last  = $u->nom    ?? $u->last_name  ?? '';
        @endphp

        <div class="flex-1 flex items-center gap-2 relative"
            x-data="{show: false}" @click.outside="show = false" @keydown.escape.window="show = false">

            <button type="button" class="shrink-0" @click="show = !show"
                aria-haspopup="menu" :aria-expanded="show">
                <x-user-initials :first="$first" :last="$last" class="w-9 h-9 text-sm"/>
            </button>
            <span x-cloak
                class="text-sm text-light overflow-hidden whitespace-nowrap transition-all duration-200"
                :class="{{ $collapsed }} ? 'opacity-0 translate-x-2 w-0' : 'opacity-100 translate-x-0 w-auto'">
                {{ Str::limit(trim($first.' '.$last), 24) }}
            </span>

            <div x-cloak x-show="show" x-transition.origin-bottom-left
                class="absolute left-full ml-2 bottom-3 w-44 bg-white text-secondary-900 shadow-lg ring-1 ring-secondary-500/20 z-50">
                <a href="{{ route('profile.edit') }}"
                    class="block px-3 py-2 hover:bg-primary-100 transition-colors">Profile</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-3 py-2 text-red-600 hover:bg-primary-100 transition-colors"
                        title="{{ __('Log out') }}">
                        Log out
                    </button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}"
            :class="{{ $collapsed }} ? 'hidden' : 'block'">
            @csrf
            <button type="submit"
                    class="p-2 rounded hover:bg-secondary-500"
                    title="{{ __('Log out') }}">
                <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6 text-primary-300"/>
            </button>
        </form>
    </div>
</nav>
