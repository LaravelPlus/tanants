<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use LaravelPlus\Tenants\Models\OrganizationInvitation;

final class InvitationReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public readonly OrganizationInvitation $invitation,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'organization_invitation',
            'organization_name' => $this->invitation->organization->name,
            'role' => $this->invitation->role,
            'token' => $this->invitation->token,
            'message' => "You've been invited to join {$this->invitation->organization->name} as a {$this->invitation->role}.",
        ];
    }
}
