<?php

namespace App\Services;

use App\Mail\TaskNotificationMail;
use App\Models\Notification;
use App\Models\NotificationPref;
use App\Models\Tache;
use App\Models\Utilisateur;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function notifyTask(
        Utilisateur $user,
        Tache $task,
        string $type,
        string $message
    ): ?Notification {
        $prefs = NotificationPref::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['email_enabled' => true, 'in_app_enabled' => true]
        );

        if (! $prefs->in_app_enabled) {
            return null;
        }

        $notification = Notification::create(
            [
                'id_utilisateur' => $user->id_utilisateur,
                'id_projet' => $task->id_projet,
                'id_tache' => $task->id_tache,
                'type' => $type,
                'message' => $message,
                'created_at'=> Carbon::now(),
            ]
        );

        if ($prefs->email_enabled && !empty($user->email)) {
            Mail::to($user->email)->queue(
                new TaskNotificationMail($notification)
            );
        }

        return $notification;
    }

    public static function checkDeadlines(): void
    {
        $today = Carbon::today();
        $soon = Carbon::today()->addDays(2);

        $soonTasks = Tache::whereNotNull('deadline')
            ->whereBetween('deadline', [$today, $soon])
            ->whereIn('status', ['todo', 'in_progress'])
            ->get();

        foreach ($soonTasks as $task) {
            if (!$task->assignee) {
                continue;
            }

            self::notifyTask(
                $task->assignee,
                $task,
                'deadline_soon',
                "Task \"{$task->titre}\" is approaching its due date ({$task->deadline->format('d/m/Y')})."
            );
        }

        $overdueTasks = Tache::whereNotNull('deadline')
            ->where('deadline', '<', $today)
            ->whereIn('status', ['todo', 'in_progress'])
            ->get();

        foreach ($overdueTasks as $task) {
            if (!$task->assignee) {
                continue;
            }

            self::notifyTask(
                $task->assignee,
                $task,
                'overdue',
                "Task \"{$task->titre}\" is overdue (due date {$task->deadline->format('d/m/Y')})."
            );
        }
    }

    public static function taskUpdated(Tache $task, array $changed): void
    {
        if (!$task->assignee) {
            return;
        }

        $changedFields = implode(', ', array_keys($changed));
        $message = "Task \"{$task->titre}\" has been updated (changed: {$changedFields}).";

        self::notifyTask(
            $task->assignee,
            $task,
            'task_updated',
            $message
        );
    }
}
