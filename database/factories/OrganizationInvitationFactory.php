<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use LaravelPlus\Tenants\Models\Organization;
use LaravelPlus\Tenants\Models\OrganizationInvitation;

/**
 * @extends Factory<OrganizationInvitation>
 */
final class OrganizationInvitationFactory extends Factory
{
    protected $model = OrganizationInvitation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'organization_id' => Organization::factory(),
            'email' => fake()->unique()->safeEmail(),
            'role' => 'member',
            'token' => Str::random(40),
            'invited_by' => null,
            'accepted_at' => null,
            'declined_at' => null,
            'expires_at' => now()->addHours(72),
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now(),
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn (array $attributes): array => [
            'declined_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
