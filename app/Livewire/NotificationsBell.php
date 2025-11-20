<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationsBell extends Component
{
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $this->notifications = Notification::where('id_utilisateur', $user->id_utilisateur)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $this->unreadCount = Notification::where('id_utilisateur', $user->id_utilisateur)
            ->unread()
            ->count();
    }

    public function markAsRead(int $idNotification)
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        $notification = Notification::where('id_notification', $idNotification)
            ->where('id_utilisateur', $user->id_utilisateur)
            ->first();

        if ($notification && !$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-bell');
    }
}
