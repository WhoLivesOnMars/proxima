<?php

namespace App\Http\Controllers;

use App\Models\ProjectInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectInvitationController extends Controller
{
    public function accept(string $token, Request $request)
    {
        $invitation = ProjectInvitation::with('projet')
            ->where('token', $token)
            ->firstOrFail();

        $project = $invitation->projet;

        if (Auth::check()) {
            $user = Auth::user();

            $alreadyMember =
                $project->owner_id === $user->id_utilisateur ||
                $project->members()
                    ->where('utilisateur.id_utilisateur', $user->id_utilisateur)
                    ->exists();

            if (! $alreadyMember) {
                $project->members()->syncWithoutDetaching([$user->id_utilisateur]);
            }

            $invitation->delete();

            return redirect()
                ->route('projects.show', $project)
                ->with('ok', 'You have joined the project.');
        }

        $request->session()->put('invite_token', $token);

        return redirect()
            ->route('register')
            ->with('status', 'Create an account to join the project.');
    }
}
