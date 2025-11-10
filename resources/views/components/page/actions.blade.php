@props([
    'createHref' => null,
    'createLabel' => 'New',
    'editBase' => null,
    'store' => 'selection',
])

<div class="flex items-center gap-3">
    @if($createHref)
        <a href="{{ $createHref }}"
            class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
            + {{ $createLabel }}
        </a>
    @endif

    @if($editBase)
        <template x-if="$store.{{ $store }}.id">
            <a :href="'{{ url($editBase) }}/' + $store.{{ $store }}.id + '/edit'"
                class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
                Edit
            </a>
        </template>

        <template x-if="$store.{{ $store }}.id">
            <form :action="'{{ url($editBase) }}/' + $store.{{ $store }}.id"
                method="POST" class="inline"
                onsubmit="return confirm('Delete this item?')">
                @csrf @method('DELETE')
                <button class="inline-flex w-fit rounded-md px-7 py-1 bg-primary-500 hover:bg-primary-700 text-white">
                    Delete
                </button>
            </form>
        </template>
    @endif

    {{ $slot }}
</div>
