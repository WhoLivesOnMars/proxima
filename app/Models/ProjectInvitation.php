<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectInvitation extends Model
{
    protected $fillable = ['id_projet', 'email', 'token'];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }
}
