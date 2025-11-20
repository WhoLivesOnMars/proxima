<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Projet;
use App\Models\Sprint;
use App\Models\Epic;
use App\Models\Tache;

class Utilisateur extends Authenticatable
{
    use HasFactory;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Notifiable;

    protected $table = 'utilisateur';
    protected $primaryKey = 'id_utilisateur';
    public $timestamps = false;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    public function projets()
    {
        return $this->hasMany(Projet::class, 'owner_id', 'id_utilisateur');
    }

    protected static function booted()
    {
        static::created(function (Utilisateur $user) {
            $project = Projet::factory()
                ->onboarding()
                ->create([
                    'owner_id' => $user->id_utilisateur,
                ]);

            $sprint = Sprint::factory()
                ->firstSprint()
                ->for($project, 'projet')
                ->create();

            $epic = Epic::factory()
                ->firstEpic()
                ->for($project, 'projet')
                ->create();

            DB::table('epic_sprint')->insert([
                'id_epic' => $epic->id_epic,
                'id_sprint' => $sprint->id_sprint,
                'id_projet' => $project->id_projet,
            ]);

            Tache::factory()
                ->checkProxima()
                ->for($project, 'projet')
                ->for($sprint, 'sprint')
                ->for($epic, 'epic')
                ->create([
                    'id_utilisateur' => $user->id_utilisateur,
                ]);
        });
    }
}
