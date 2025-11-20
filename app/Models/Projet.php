<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Projet extends Model
{
    protected $table = 'projet';
    protected $primaryKey = 'id_projet';
    public $incrementing = true;

    protected $fillable = [
        'nom',
        'description',
        'owner_id',
        'status',
        'visibility',
        'share_token',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_projet';
    }

    public function owner() {
        return $this->belongsTo(Utilisateur::class, 'owner_id', 'id_utilisateur');
    }

    public function sprints() {
        return $this->hasMany(Sprint::class, 'id_projet', 'id_projet');
    }

    public function firstSprint() {
        return $this->hasOne(Sprint::class, 'id_projet', 'id_projet')->orderBy('start_date', 'asc');
    }

    public function currentSprint()
    {
        return $this->hasOne(Sprint::class, 'id_projet', 'id_projet')
            ->whereDate('start_date', '<=', now())
            ->whereRaw("
                DATE_ADD(
                    start_date,
                    INTERVAL (
                        CASE
                            WHEN duree BETWEEN 1 AND 6 THEN duree * 7
                            ELSE duree
                        END
                    ) - 1 DAY
                ) >= CURDATE()
            ")
            ->orderBy('start_date', 'desc');
    }

    public function epics() {
        return $this->hasMany(Epic::class,  'id_projet','id_projet');
    }

    public function taches() {
        return $this->hasMany(Tache::class, 'id_projet','id_projet');
    }

    public function proposedTasks() {
        return $this->hasMany(TacheProposee::class, 'id_projet','id_projet');
    }

    public function members() {
        return $this->belongsToMany(Utilisateur::class, 'membre_projet', 'id_projet', 'id_utilisateur')
            ->withPivot('role');
    }

    public function scopeVisibleTo(Builder $q, Utilisateur $user): Builder {
        return $q->where('owner_id', $user->id_utilisateur)
            ->orWhere('visibility', 'public')
            ->orWhereIn('id_projet', function($sub) use ($user){
                $sub->from('membre_projet')->select('id_projet')
                    ->where('id_utilisateur', $user->id_utilisateur);
            });
    }

    public function scopeOwnedOrMember(Builder $q, int $userId): Builder
    {
        return $q->where(function ($qq) use ($userId) {
            $qq->where('owner_id', $userId)
            ->orWhereHas('members', fn($m) =>
                $m->where('membre_projet.id_utilisateur', $userId)
            );
        });
    }
}
