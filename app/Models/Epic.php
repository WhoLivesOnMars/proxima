<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Epic extends Model
{
    use HasFactory;

    protected $table = 'epic';
    protected $primaryKey = 'id_epic';
    public $timestamps = false;

    protected $fillable = ['id_projet','nom'];

    public function projet()  { return $this->belongsTo(Projet::class, 'id_projet','id_projet'); }

    public function sprints()
    {
        return $this->belongsToMany(
            Sprint::class,
            'epic_sprint',
            'id_epic',
            'id_sprint'
        )->withPivot('id_projet');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'id_epic', 'id_epic')
                ->with('assignee');
    }
}
