<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if($bg)
            <link rel="preload" as="image" href="{{ asset($bg) }}">
        @endif
    </head>
    <body class="font-sans antialiased bg-surface text-text">

        <div class="grid min-h-screen grid-cols-1 sm:grid-cols-5">
            <div class="relative hidden sm:block sm:col-span-3">
                @if($bg)
                    <img src="{{ asset($bg) }}"
                        alt="Background"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                        aria-hidden="true">
                @endif
                <div class="absolute inset-0 bg-black/30"></div>

                <a href="{{ url('/') }}"
                    class="absolute left-8 top-8 text-white text-3xl font-bold">
                    Logo
                </a>
            </div>

            <div class="flex items-center justify-center p-6 sm:col-span-2">
                <main class="w-full max-w-md">
                    {{ $slot }}
                </main>
            </div>
        </div>

    </body>
</html>
