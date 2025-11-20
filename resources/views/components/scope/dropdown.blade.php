@props([
    'scope' => 'all',
    'routeName',
    'param' => 'scope',
    'options' => [
        'all' => 'ALL',
        'owned' => 'Created by me',
        'shared' => 'Shared with me',
    ],
])

@php
    $label = $options[$scope] ?? $options['all'];

    $query = request()->except('page');
@endphp

<x-dropdown align="left" width="48">
    <x-slot name="trigger">
        <button type="button"
            class="inline-flex items-center gap-1 font-bold text-xl uppercase tracking-wide">
            <span>{{ $label }}</span>
            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                <path d="M5.25 7.5 10 12.25 14.75 7.5h-9.5Z"/>
            </svg>
        </button>
    </x-slot>

    <x-slot name="content">
        @foreach ($options as $val => $text)
            @php
                $newQuery = $query;

                if ($val === 'all') {
                    unset($newQuery[$param]);
                } else {
                    $newQuery[$param] = $val;
                }

                $href = route($routeName, $newQuery);
            @endphp

            <x-dropdown-link
                href="{{ $href }}"
                class="{{ $scope === $val ? 'font-semibold text-primary-600' : '' }}"
            >
                {{ $text }}
            </x-dropdown-link>
        @endforeach
    </x-slot>
</x-dropdown>
