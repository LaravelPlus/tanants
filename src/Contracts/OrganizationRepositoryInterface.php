<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelPlus\Tenants\Models\Organization;

interface OrganizationRepositoryInterface
{
    public function query(): Builder;

    public function find(int $id): ?Organization;

    public function findOrFail(int $id): Organization;

    public function findBySlug(string $slug): ?Organization;

    public function findByUuid(string $uuid): ?Organization;

    public function create(array $data): Organization;

    public function update(Organization $organization, array $data): Organization;

    public function delete(Organization $organization): bool;

    public function all(): Collection;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function forUser(int $userId): Collection;

    public function personalForUser(int $userId): ?Organization;
}
