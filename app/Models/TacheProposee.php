<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TacheProposee extends Model
{
    protected $table = 'tache_proposee';
    protected $primaryKey = 'id_tache_prop';
    public $timestamps = true;

    protected $fillable = [
        'id_projet','created_by','id_utilisateur','id_epic','id_sprint',
        'titre','description','deadline','approval','decided_by','decided_at','decided_comment',
    ];

    public function projet() { return $this->belongsTo(Projet::class, 'id_projet', 'id_projet'); }
    public function creator() { return $this->belongsTo(Utilisateur::class, 'created_by', 'id_utilisateur'); }
    public function assignee() { return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur'); }
    public function epic() { return $this->belongsTo(Epic::class, 'id_epic', 'id_epic'); }
    public function sprint() { return $this->belongsTo(Sprint::class, 'id_sprint', 'id_sprint'); }
}
