<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Console\Commands;

use Illuminate\Console\Command;
use LaravelPlus\Tenants\Models\OrganizationInvitation;

final class CleanupInvitationsCommand extends Command
{
    protected $signature = 'tenants:cleanup-invitations
                            {--days=30 : Delete expired invitations older than this many days}';

    protected $description = 'Remove expired organization invitations';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $count = OrganizationInvitation::expired()
            ->where('expires_at', '<', now()->subDays($days))
            ->delete();

        $this->info("Deleted {$count} expired invitation(s) older than {$days} day(s).");

        return self::SUCCESS;
    }
}
