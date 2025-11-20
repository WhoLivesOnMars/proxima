<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Sprint extends Model
{
    use HasFactory;

    protected $table = 'sprint';
    protected $primaryKey = 'id_sprint';
    public $timestamps = false;

    protected $fillable = ['id_projet','nom','start_date','duree'];
    protected $casts = ['start_date' => 'date'];

    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet', 'id_projet');
    }

    public function scopeActiveToday(Builder $q): Builder
    {
        return $q
            ->whereDate('start_date', '<=', now())
            ->whereRaw('DATE_ADD(start_date, INTERVAL duree WEEK) > CURDATE()');
    }

    public function getEndDateAttribute(): ?Carbon
    {
        if (!$this->start_date || !$this->duree) return null;
        return Carbon::parse($this->start_date)->copy()->addWeeks((int)$this->duree);
    }

    public function epics()
    {
        return $this->belongsToMany(
            Epic::class,
            'epic_sprint',
            'id_sprint',
            'id_epic'
            )->withPivot('id_projet');
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'id_sprint', 'id_sprint');
    }
}
