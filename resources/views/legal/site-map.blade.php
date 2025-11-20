<x-app-layout title="Sitemap">
    <div class="max-w-4xl mx-auto py-8 space-y-8 text-slate-800">

        <h1 class="text-3xl font-bold mb-6">Sitemap</h1>

        <p class="text-slate-700">
            Find here all pages and sections available on <strong>PROXIMA</strong>.
        </p>

        <section>
            <h2 class="text-2xl font-semibold mb-3">Home</h2>

            <div class="pl-6 space-y-2">
                <a href="{{ url('/') }}" class="hover:underline block">
                    Welcome page (Home)
                </a>
                <a href="{{ route('login') }}" class="hover:underline block">
                    Login
                </a>
                <a href="{{ route('register') }}" class="hover:underline block">
                    Register
                </a>
                <a href="{{ route('dashboard') }}" class="hover:underline block">
                    Dashboard
                </a>
            </div>
        </section>

        <!-- Project Management -->
        <section>
            <h2 class="text-2xl font-semibold mb-3">Project Management</h2>

            <div class="pl-6 space-y-2">
                <a href="{{ route('projects.index') }}" class="hover:underline block">
                    Projects list
                </a>

                <span class="block">Project pages:</span>
                <div class="pl-6 space-y-2">
                    <span class="block">• Sprints</span>
                    <span class="block">• Epics</span>
                    <span class="block">• Tasks</span>
                    <span class="block">• Task proposals</span>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-3">Task Views</h2>

            <div class="pl-6 space-y-2">
                <a href="{{ route('kanban.index') }}" class="hover:underline block">
                    Kanban board
                </a>
                <a href="{{ route('roadmap.index') }}" class="hover:underline block">
                    Roadmap
                </a>
                <a href="{{ route('reports.index') }}" class="hover:underline block">
                    Reports
                </a>
            </div>
        </section>

        <!-- Account -->
        <section>
            <h2 class="text-2xl font-semibold mb-3">Account</h2>

            <div class="pl-6 space-y-2">
                <a href="{{ route('profile.edit') }}" class="hover:underline block">
                    Profile settings
                </a>
            </div>
        </section>

        <!-- Legal -->
        <section>
            <h2 class="text-2xl font-semibold mb-3">Legal information</h2>

            <div class="pl-6 space-y-2">
                <a href="{{ route('legal.notice') }}" class="hover:underline block">
                    Legal notice
                </a>
                <a href="{{ route('privacy.policy') }}" class="hover:underline block">
                    GDPR / Privacy policy
                </a>
                <a href="{{ route('accessibility') }}" class="hover:underline block">
                    Accessibility
                </a>
            </div>
        </section>

        <p class="text-sm text-slate-500 pt-4 border-t">
            Last updated: 20 November 2025
        </p>

    </div>
</x-app-layout>
