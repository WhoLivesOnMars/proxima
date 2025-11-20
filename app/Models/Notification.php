<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'id_notification';
    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'id_projet',
        'id_tache',
        'type',
        'message',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    protected $dates = ['read_at', 'created_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Tache::class, 'id_tache', 'id_tache');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
