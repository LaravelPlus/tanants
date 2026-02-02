<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use LaravelPlus\Tenants\Models\OrganizationInvitation;

final class InvitationAccepted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly OrganizationInvitation $invitation
    ) {}
}
