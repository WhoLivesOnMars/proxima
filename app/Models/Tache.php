<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $table = 'tache';
    protected $primaryKey = 'id_tache';

    protected $fillable = [
        'id_projet','id_epic','id_sprint','id_utilisateur',
        'titre','description','start_date','deadline','status', 'attachment_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function projet() {
        return $this->belongsTo(Projet::class, 'id_projet','id_projet');
    }

    public function epic() {
        return $this->belongsTo(Epic::class, 'id_epic','id_epic');
    }

    public function sprint() {
        return $this->belongsTo(Sprint::class, 'id_sprint','id_sprint');
    }

    protected static function booted()
    {
        static::updated(function (Tache $task) {
            $dirty = $task->getChanges();

            unset($dirty['updated_at']);

            if (!empty($dirty)) {
                NotificationService::taskUpdated($task, $dirty);
            }
        });
    }

    public function assignee() {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur','id_utilisateur');
    }
}
