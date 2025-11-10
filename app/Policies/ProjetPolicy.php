<?php

namespace App\Policies;

use App\Models\Projet;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class ProjetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Utilisateur $utilisateur): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Utilisateur $utilisateur, Projet $projet): bool
    {
        if ($projet->owner_id === $utilisateur->id_utilisateur) {
            return true;
        }

        if ($projet->members()->where('membre_projet.id_utilisateur', $utilisateur->id_utilisateur)->exists()) {
            return true;
        }

        if (request()->has('token') && request()->get('token') === $projet->share_token) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Utilisateur $utilisateur): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Utilisateur $utilisateur, Projet $projet): bool
    {
        return $projet->owner_id === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Utilisateur $utilisateur, Projet $projet): bool
    {
        return $projet->owner_id === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Utilisateur $utilisateur, Projet $projet): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Utilisateur $utilisateur, Projet $projet): bool
    {
        return false;
    }

    public function manage(Utilisateur $utilisateur, Projet $projet)
    {
        return $projet->owner_id === $utilisateur->id_utilisateur;
    }
}
