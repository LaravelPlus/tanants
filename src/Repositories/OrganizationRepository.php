<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use LaravelPlus\Tenants\Contracts\OrganizationRepositoryInterface;
use LaravelPlus\Tenants\Models\Organization;

final class OrganizationRepository implements OrganizationRepositoryInterface
{
    public private(set) string $modelClass = Organization::class;

    public function query(): Builder
    {
        return $this->modelClass::query();
    }

    public function find(int $id): ?Organization
    {
        return $this->query()->find($id);
    }

    public function findOrFail(int $id): Organization
    {
        return $this->query()->findOrFail($id);
    }

    public function findBySlug(string $slug): ?Organization
    {
        return $this->query()->where('slug', $slug)->first();
    }

    public function findByUuid(string $uuid): ?Organization
    {
        return $this->query()->where('uuid', $uuid)->first();
    }

    public function create(array $data): Organization
    {
        return $this->query()->create($data);
    }

    public function update(Organization $organization, array $data): Organization
    {
        $organization->update($data);

        return $organization->fresh();
    }

    public function delete(Organization $organization): bool
    {
        return (bool) $organization->delete();
    }

    public function all(): Collection
    {
        return $this->query()->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->latest()->paginate($perPage);
    }

    public function forUser(int $userId): Collection
    {
        return $this->query()
            ->whereHas('members', fn (Builder $q) => $q->where('user_id', $userId))
            ->get();
    }

    public function personalForUser(int $userId): ?Organization
    {
        return $this->query()
            ->where('owner_id', $userId)
            ->where('is_personal', true)
            ->first();
    }
}
