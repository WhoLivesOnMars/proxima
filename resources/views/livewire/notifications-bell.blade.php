<div
    x-data="{ open: false }"
    class="relative"
>
    <button
        type="button"
        class="relative inline-flex items-center justify-center p-2 rounded-full hover:bg-primary-200"
        @click="open = !open"
        wire:click="loadNotifications"
    >
        <x-heroicon-o-bell class="w-6 h-6 text-secondary-900"/>

        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-rose-500 text-[10px] font-semibold text-white flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition
        @click.outside="open = false"
        class="absolute right-0 mt-2 w-80 max-h-96 bg-white rounded-xl shadow-lg ring-1 ring-slate-200 overflow-hidden z-50"
    >
        <div class="px-3 py-2 border-b border-slate-100 text-xs font-semibold text-slate-600">
            Notifications
        </div>

        <div class="max-h-80 overflow-y-auto text-sm">
            @forelse($notifications as $notif)
                <button
                    type="button"
                    class="w-full text-left px-3 py-2 flex flex-col gap-0.5 hover:bg-slate-50 {{ $notif->read_at ? 'text-slate-500' : 'text-slate-800 font-medium' }}"
                    wire:click="markAsRead({{ $notif->id_notification }})"
                >
                    <div class="text-xs uppercase tracking-wide text-slate-400">
                        {{ ucfirst(str_replace('_', ' ', $notif->type)) }}
                    </div>
                    <div>{{ $notif->message }}</div>
                    <div class="text-[11px] text-slate-400">
                        {{ $notif->created_at?->format('Y-m-d H:i') }}
                    </div>
                </button>
            @empty
                <div class="px-3 py-3 text-xs text-slate-500">
                    No notifications yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
