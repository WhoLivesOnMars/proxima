<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPref extends Model
{
    protected $table = 'notification_pref';
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'email_enabled',
        'in_app_enabled',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'in_app_enabled'  => 'boolean',
    ];
}
