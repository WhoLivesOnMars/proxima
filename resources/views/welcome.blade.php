<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>PROXIMA – Task & Project Management Tool</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
        </style>
    @endif
</head>
<body class="min-h-screen text-white antialiased">
<div class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 -z-10">
        <img
            src="{{ asset('img/proxima-hero.jpg') }}"
            alt="Background illustration"
            class="h-full w-full object-cover opacity-80"
        >

        <div class="absolute inset-0 bg-slate-950/60"></div>

        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/40 via-slate-900/10 to-indigo-900/30"></div>
    </div>

    <div class="w-full max-w-6xl px-6 lg:px-10 py-10">
        <header class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="h-9 w-9 rounded-xl bg-amber-400/90 flex items-center justify-center shadow-lg">
                    <span class="font-semibold text-slate-950 text-sm">PX</span>
                </div>
                <div>
                    <div class="text-sm font-semibold tracking-wide">
                        PROXIMA
                    </div>
                    <div class="text-[11px] text-slate-300/80">
                        Task &amp; Project Management Tool
                    </div>
                </div>
            </div>

            <span class="hidden text-[11px] px-3 py-1 rounded-full border border-white/15 text-slate-200/80 lg:inline-flex">
                Kanban • Roadmap • Reports
            </span>
        </header>

        <main class="mt-12 lg:mt-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <section>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-semibold tracking-tight mb-4">
                    Bring clarity to your projects
                </h1>

                <p class="text-sm sm:text-base text-slate-200/85 max-w-xl mb-6">
                    PROXIMA helps your team manage sprints, epics and tasks
                    in a single workspace. Focus on what matters, we handle the flow.
                </p>

                <ul class="text-xs sm:text-[13px] text-slate-200/85 space-y-1.5 mb-8">
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Real-time task updates for all project members
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                        Sprints, epics and deadlines in one board
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                        Attachments, assignees and statuses at a glance
                    </li>
                </ul>

                <div class="flex flex-wrap items-center gap-3">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-medium
                                   bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30
                                   hover:bg-amber-300 transition-colors"
                        >
                            Open dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-medium
                                   bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30
                                   hover:bg-amber-300 transition-colors"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-medium
                                       border border-white/20 text-slate-50/90 bg-white/5
                                       hover:bg-white/10 transition-colors"
                            >
                                Create account
                            </a>
                        @endif
                    @endauth
                </div>
            </section>

            <div class="hidden lg:block w-[460px] rounded-2xl border border-white/15 bg-white/5 backdrop-blur-xl shadow-2xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">
                            Project
                        </div>
                        <div class="text-xs font-semibold text-white">SAE 501 • Sprint 1</div>
                    </div>

                    <div class="relative">
                        <div class="p-1.5 rounded-full bg-white/20 text-white">
                            <x-heroicon-o-bell class="w-4 h-4"/>
                        </div>
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[9px] font-bold rounded-full px-1.5">
                            3
                        </span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <div class="flex-1">
                        <div class="rounded-t-lg px-2 py-1.5 text-[10px] font-semibold text-white bg-[#F06392] flex items-center justify-between">
                            <span>To do</span>
                            <span class="text-[9px] bg-white/15 rounded-full px-1.5">3</span>
                        </div>
                        <div class="space-y-1.5 bg-white/70 rounded-b-lg px-2 py-1.5">

                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 1</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">User permissions UI</div>

                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>25 Nov</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">DK</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 2</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">Sprint settings modal</div>
                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>12 Dec</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">AM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="rounded-t-lg px-2 py-1.5 text-[10px] font-semibold text-white bg-[#2979B8] flex items-center justify-between">
                            <span>In progress</span>
                            <span class="text-[9px] bg-white/15 rounded-full px-1.5">2</span>
                        </div>
                        <div class="space-y-1.5 bg-white/70 rounded-b-lg px-2 py-1.5">
                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 1</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">Kanban filters</div>
                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>15 Nov</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">DK</span>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 3</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">Realtime sync</div>
                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>18 Nov</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">MB</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1">
                        <div class="rounded-t-lg px-2 py-1.5 text-[10px] font-semibold text-white bg-[#159D7C] flex items-center justify-between">
                            <span>Done</span>
                            <span class="text-[9px] bg-white/15 rounded-full px-1.5">2</span>
                        </div>
                        <div class="space-y-1.5 bg-white/70 rounded-b-lg px-2 py-1.5">
                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 1</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">Board layout</div>
                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>25 Oct</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">DK</span>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-md bg-white shadow-sm px-2 py-1">
                                <div class="text-[10px] text-slate-500 font-semibold mb-0.5">Epic 2</div>
                                <div class="text-[11px] font-semibold text-slate-800 truncate">File attachments</div>
                                <div class="flex items-center justify-between mt-1 text-[9px] text-slate-500">
                                    <span></span>
                                    <div class="flex items-center w-full justify-between">
                                        <span>11 Oct</span>
                                        <span class="w-5 h-5 flex items-center justify-center rounded-full bg-slate-200 text-[9px] text-slate-700 font-bold">AM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
