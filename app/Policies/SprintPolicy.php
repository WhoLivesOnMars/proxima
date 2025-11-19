<?php

namespace App\Policies;

use App\Models\Sprint;
use App\Models\Utilisateur;
use Illuminate\Auth\Access\Response;

class SprintPolicy
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
    public function view(Utilisateur $utilisateur, Sprint $sprint): bool
    {
        $project = $sprint->projet;

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
    public function update(Utilisateur $utilisateur, Sprint $sprint): bool
    {
        $project = $sprint->projet;

        return $project->owner_id === $utilisateur->id_utilisateur;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Utilisateur $utilisateur, Sprint $sprint): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Utilisateur $utilisateur, Sprint $sprint): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Utilisateur $utilisateur, Sprint $sprint): bool
    {
        return false;
    }
}
