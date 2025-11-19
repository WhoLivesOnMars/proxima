<?php

namespace App\Mail;

use App\Models\ProjectInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ProjectInvitation $invitation;

    /**
     * Create a new message instance.
     */
    public function __construct(ProjectInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $projectName = $this->invitation->projet->nom ?? 'project';

        return new Envelope(
            subject: 'Invitation to join project "' . $projectName . '"',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $project   = $this->invitation->projet;
        $acceptUrl = route('invites.accept', $this->invitation->token);

        return new Content(
            markdown: 'emails.project_invitation',
            with: [
                'invitation' => $this->invitation,
                'project' => $project,
                'acceptUrl' => $acceptUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
