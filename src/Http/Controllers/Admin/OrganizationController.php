<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LaravelPlus\Tenants\Contracts\OrganizationServiceInterface;
use LaravelPlus\Tenants\Http\Requests\Admin\StoreOrganizationRequest;
use LaravelPlus\Tenants\Http\Requests\Admin\UpdateOrganizationRequest;
use LaravelPlus\Tenants\Models\Organization;

final class OrganizationController
{
    public function __construct(
        private(set) OrganizationServiceInterface $organizationService,
    ) {}

    private function authorizeAdmin(): void
    {
        $user = auth()->user();

        if (!$user || !array_any(['super-admin', 'admin'], fn (string $role): bool => $user->hasRole($role))) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function index(Request $request): Response
    {
        $this->authorizeAdmin();

        $organizations = $this->organizationService->list(
            perPage: 15,
            search: $request->get('search'),
        );

        return Inertia::render('admin/Organizations/Index', [
            'organizations' => $organizations,
            'filters' => [
                'search' => $request->get('search', ''),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorizeAdmin();

        return Inertia::render('admin/Organizations/Create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $organization = $this->organizationService->create(
            $request->validated(),
            $request->user()->id,
        );

        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::log('organization.created', $organization, null, [
                'name' => $organization->name,
            ]);
        }

        return redirect()->route('admin.organizations.index')
            ->with('status', 'Organization created successfully.');
    }

    public function show(Organization $organization): Response
    {
        $this->authorizeAdmin();

        $organization->load(['owner', 'members', 'invitations' => fn ($q) => $q->pending()]);

        return Inertia::render('admin/Organizations/Show', [
            'organization' => $organization,
        ]);
    }

    public function edit(Organization $organization): Response
    {
        $this->authorizeAdmin();

        $organization->load(['owner', 'members', 'invitations' => fn ($q) => $q->pending()]);

        return Inertia::render('admin/Organizations/Edit', [
            'organization' => $organization,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $this->authorizeAdmin();

        $oldValues = ['name' => $organization->name, 'description' => $organization->description];

        $this->organizationService->update($organization, $request->validated());

        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::log('organization.updated', $organization, $oldValues, [
                'name' => $organization->name,
            ]);
        }

        return redirect()->route('admin.organizations.index')
            ->with('status', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorizeAdmin();

        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::log('organization.deleted', $organization, [
                'name' => $organization->name,
            ]);
        }

        $this->organizationService->delete($organization);

        return redirect()->route('admin.organizations.index')
            ->with('status', 'Organization deleted successfully.');
    }
}
