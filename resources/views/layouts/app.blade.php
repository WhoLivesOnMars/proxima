<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @if (isset($title))
            {{ $title }} - {{ config('app.name', 'PROXIMA') }}
        @else
            {{ config('app.name', 'PROXIMA') }}
        @endif
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script>
        (function () {
            try {
                const saved = localStorage.getItem('sidebar_collapsed');
                const collapsed = saved === null ? true : (saved === 'true');
                const initialWidth = collapsed ? '5rem' : '16rem';
                document.documentElement.style.setProperty('--aside-initial-width', initialWidth);
            } catch (e) {
                document.documentElement.style.setProperty('--aside-initial-width', '5rem');
            }
        })();
    </script>
</head>
<body
    x-data
    class="h-dvh bg-background text-dark"
>
    <div class="grid h-full"
        style="grid-template-rows: auto 1fr; grid-template-columns: auto 1fr;">

        <aside class="row-span-2 col-[1] h-full">
            <div class="h-full transition-[width] duration-300 ease-in-out"
                style="width: var(--aside-initial-width, 5rem)"
                :style="`width: ${$store.sidebar.collapsed ? '5rem' : '16rem'}`">
                @include('layouts.navigation')
            </div>
        </aside>

        <header class="py-3 px-4 flex items-center bg-primary-100 backdrop-blur">
            <div class="text-xl font-bold select-none">PROXIMA</div>
        </header>

        <main class="col-[2] row-[2] bg-surface overflow-y-auto p-6">
            @isset($header)
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endisset

            {{ $slot }}
        </main>
    </div>
    @livewireScripts

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('selection', { id: null });

            Alpine.store('sidebar', {
                collapsed: true,

                init() {
                    const saved = localStorage.getItem('sidebar_collapsed');
                    if (saved !== null) {
                        this.collapsed = saved === 'true';
                    }
                },

                toggle() {
                    this.collapsed = !this.collapsed;
                    localStorage.setItem('sidebar_collapsed', this.collapsed);
                },
            });

            Alpine.store('sidebar').init();
        });
    </script>

    @stack('scripts')
</body>
</html>
