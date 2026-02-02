<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use LaravelPlus\Tenants\Models\Organization;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->optional()->sentence(),
            'owner_id' => null,
            'is_personal' => false,
            'logo_path' => null,
            'metadata' => null,
        ];
    }

    public function personal(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_personal' => true,
        ]);
    }

    public function withOwner(int $userId): static
    {
        return $this->state(fn (array $attributes): array => [
            'owner_id' => $userId,
        ]);
    }
}
