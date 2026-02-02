<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Enums;

enum MemberRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Member = 'member';

    /**
     * Get member roles from configuration.
     *
     * @return array<self>
     */
    public static function fromConfig(): array
    {
        $roles = config('tenants.member_roles', []);

        return array_filter(
            array_map(
                fn (string $role) => self::tryFrom($role),
                $roles
            )
        );
    }

    /**
     * Get the human-readable label for the role.
     */
    public function label(): string
    {
        return ucfirst($this->value);
    }
}
