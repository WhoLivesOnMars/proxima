<?php

namespace App\Policies;

use App\Models\Tache;
use App\Models\TacheProposee;
use App\Models\Projet;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class TachePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Utilisateur $utilisateur): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Utilisateur $utilisateur, Tache $tache): bool
    {
        $project = $tache->projet;
        return $project->owner_id === $utilisateur->id_utilisateur
            || $project->members()->where('membre_projet.id_utilisateur', $utilisateur->id_utilisateur)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Utilisateur $utilisateur, Projet $project): bool
    {
        if ($project->owner_id === $utilisateur->id_utilisateur) {
            return true;
        }

        return $project->members()
            ->where('membre_projet.id_utilisateur', $utilisateur->id_utilisateur)->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Utilisateur $utilisateur, Tache $tache): bool
    {
        if (request()->has('token')) {
            return false;
        }

        $project = $tache->projet;

        if ($project->owner_id === $utilisateur->id_utilisateur) {
            return true;
        }

        return $tache->id_utilisateur === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Utilisateur $utilisateur, Tache $tache): bool
    {
        $project = $tache->projet;

        return $project->owner_id === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Utilisateur $utilisateur, Tache $tache): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Utilisateur $utilisateur, Tache $tache): bool
    {
        return false;
    }

    public function propose(Utilisateur $u, Projet $project): bool
    {
        if ($project->owner_id === $u->id_utilisateur) {
            return true;
        }

        return $project->members()
            ->where('membre_projet.id_utilisateur', $u->id_utilisateur)
            ->exists();
    }

    public function moderate(Utilisateur $u, TacheProposee $prop): bool
    {
        return $prop->projet->owner_id === $u->id_utilisateur;
    }

    public function viewProposals(Utilisateur $u, Projet $project): bool
    {
        if ($project->owner_id === $u->id_utilisateur) {
            return true;
        }

        return $project->members()
            ->where('membre_projet.id_utilisateur', $u->id_utilisateur)
            ->exists();
    }
}
