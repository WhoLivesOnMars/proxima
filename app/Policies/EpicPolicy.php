<?php

namespace App\Policies;

use App\Models\Epic;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class EpicPolicy
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
    public function view(Utilisateur $utilisateur, Epic $epic): bool
    {
        $project = $epic->projet;

        return $project->owner_id === $utilisateur->id_utilisateur
            || $project->members()
                ->where('membre_projet.id_utilisateur', $utilisateur->id_utilisateur)
                ->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Utilisateur $utilisateur): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Utilisateur $utilisateur, Epic $epic): bool
    {
        $project = $epic->projet;

        return $project->owner_id === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Utilisateur $utilisateur, Epic $epic): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Utilisateur $utilisateur, Epic $epic): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Utilisateur $utilisateur, Epic $epic): bool
    {
        return false;
    }
}
