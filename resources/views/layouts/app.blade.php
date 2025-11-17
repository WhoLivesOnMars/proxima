<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body
    x-data="{ collapsed: true }"
    class="h-dvh bg-background text-dark"
>
    <div class="grid h-full"
        style="grid-template-rows: auto 1fr; grid-template-columns: auto 1fr;">

        <aside class="row-span-2 col-[1] h-full"
            :style="`--aside-w: ${collapsed ? '5rem' : '16rem'}`">
            <div class="h-full"
                style="width: var(--aside-w)"
                class="transition-[width] duration-300 ease-in-out">
                @include('layouts.navigation', ['collapsedVar' => 'collapsed'])
            </div>
        </aside>

        <header class="col-[2] row-[1] h-14 bg-primary-100 backdrop-blur">
            <div class="h-full px-4 flex items-center gap-4">
                <div class="text-xl font-semibold select-none">Logo</div>

                <div class="ml-auto relative flex-none w-72 sm:w-80 md:w-96">
                    <input type="search" placeholder="Search"
                    class="w-full rounded-2xl border border-primary-300 bg-white pe-9 h-10"/>
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute right-2 top-1/2 -translate-y-1/2 text-primary-500"/>
                </div>
            </div>
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
            Alpine.store('selection', {
                id: null,
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
