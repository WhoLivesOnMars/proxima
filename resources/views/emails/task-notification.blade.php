<x-mail::message>
# Task notification

Hello {{ $notification->user?->prenom ?? '' }},

You have a new notification related to the task **"{{ $notification->task?->titre }}"**.

---

### 📌 Details
**Project:** {{ $notification->project?->nom ?? '—' }}
**Task:** {{ $notification->task?->titre ?? '—' }}
**Type:** {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
**Message:**
{{ $notification->message }}

---

<x-mail::button :url="route('kanban.index')">
Open Kanban board
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
