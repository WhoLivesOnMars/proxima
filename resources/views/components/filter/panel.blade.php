@props([
    'action',
    'persist' => [],
    'width' => 'w-[28rem]',
])

<x-dropdown align="right" :close-on-click="false" :width="$width" content-classes="p-4 bg-white">
    <x-slot name="trigger">
        <button type="button" class="inline-flex items-center gap-2 text-secondary-900">
            <x-heroicon-o-funnel class="w-5 h-5"/><span>Filter</span>
        </button>
    </x-slot>

    <x-slot name="content">
        <form method="GET" action="{{ $action }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4" @click.stop>
            @foreach($persist as $k)
                <input type="hidden" name="{{ $k }}" value="{{ request($k) }}">
            @endforeach

            {{ $slot }}

            <div class="sm:col-span-2 pt-1 flex items-center gap-2">
                <a href="{{ $action }}?{{ http_build_query(collect(request()->except('page'))->only($persist)->all()) }}"
                    class="px-4 py-2 rounded border">Reset</a>
                <button class="px-4 py-2 rounded bg-primary-500 hover:bg-primary-700 text-white">Apply</button>
            </div>
        </form>
    </x-slot>
</x-dropdown>
