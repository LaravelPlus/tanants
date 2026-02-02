<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use LaravelPlus\Tenants\Models\Organization;

interface OrganizationServiceInterface
{
    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator;

    public function create(array $data, ?int $ownerId = null): Organization;

    public function update(Organization $organization, array $data): Organization;

    public function delete(Organization $organization): bool;

    public function createPersonalOrganization(int $userId, string $userName): Organization;

    public function addMember(Organization $organization, int $userId, string $role = 'member'): void;

    public function removeMember(Organization $organization, int $userId): void;

    public function changeMemberRole(Organization $organization, int $userId, string $role): void;

    public function switchOrganization(int $userId, Organization $organization): void;
}
