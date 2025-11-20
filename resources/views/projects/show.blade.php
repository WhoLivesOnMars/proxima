<x-app-layout :title="$projet->nom">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl">
                {{ $projet->nom }}
            </h2>

            @if($projet->visibility === 'shared' && $projet->share_token)
                <div
                    x-data="{
                        copied: false,
                        async copyLink() {
                            try {
                                await navigator.clipboard.writeText(@js(route('projects.shared', $projet->share_token)));
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    }"
                    class="relative flex items-center gap-2 text-sm"
                >
                    <button
                        type="button"
                        @click="copyLink()"
                        class="px-4 py-1.5 rounded-md bg-primary-500 hover:bg-primary-700 text-white text-xs font-medium"
                    >
                        Share link
                    </button>

                    <div
                        x-show="copied"
                        x-transition
                        class="absolute right-0 top-full mt-1 rounded bg-green-100 text-green-800 text-xs px-2 py-1 shadow"
                    >
                        Link copied!
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <livewire:project-board :projet-id="$projet->id_projet" />
</x-app-layout>
