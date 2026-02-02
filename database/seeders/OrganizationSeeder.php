<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use LaravelPlus\Tenants\Models\Organization;

final class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if (!$admin) {
            return;
        }

        $organization = Organization::factory()
            ->withOwner($admin->id)
            ->create([
                'name' => 'Acme Corporation',
                'slug' => 'acme-corporation',
                'description' => 'A sample organization for development.',
            ]);

        $organization->addMember($admin, 'owner');

        Organization::factory()
            ->count(3)
            ->withOwner($admin->id)
            ->create()
            ->each(function (Organization $org) use ($admin): void {
                $org->addMember($admin, 'owner');
            });
    }
}
