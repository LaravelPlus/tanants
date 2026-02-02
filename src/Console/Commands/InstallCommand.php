<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Console\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature = 'tenants:install';

    protected $description = 'Install the Tenants package (publish config and run migrations)';

    public function handle(): int
    {
        $this->info('Installing Tenants package...');

        $this->call('vendor:publish', [
            '--tag' => 'tenants-config',
        ]);

        $this->info('Published configuration.');

        if ($this->confirm('Would you like to run migrations now?', true)) {
            $this->call('migrate');
            $this->info('Migrations completed.');
        }

        if ($this->confirm('Would you like to publish the AI skills?', true)) {
            $this->call('vendor:publish', [
                '--tag' => 'tenants-skills',
            ]);
            $this->info('AI skills published.');
        }

        $this->info('Tenants package installed successfully.');
        $this->newLine();
        $this->info('Add the HasOrganizations trait to your User model:');
        $this->line('  use LaravelPlus\Tenants\Traits\HasOrganizations;');

        return self::SUCCESS;
    }
}
