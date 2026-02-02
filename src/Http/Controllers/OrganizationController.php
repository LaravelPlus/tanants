<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use LaravelPlus\Tenants\Contracts\OrganizationServiceInterface;
use LaravelPlus\Tenants\Http\Requests\StoreOrganizationRequest;
use LaravelPlus\Tenants\Models\Organization;

final class OrganizationController
{
    public function __construct(
        private(set) OrganizationServiceInterface $organizationService,
    ) {}

    public function index(): Response
    {
        $user = auth()->user();

        $organizations = $user->organizations()
            ->withCount('members')
            ->with('owner:id,name')
            ->latest()
            ->get();

        return Inertia::render('Organizations/Index', [
            'organizations' => $organizations,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Organizations/Create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $this->organizationService->create(
            $request->validated(),
            $request->user()->id,
        );

        return redirect()->route('organizations.index')
            ->with('status', 'Organization created successfully.');
    }

    public function show(Organization $organization): Response
    {
        $user = auth()->user();

        if (!$user->belongsToOrganization($organization)) {
            abort(403, 'You are not a member of this organization.');
        }

        $organization->load(['owner:id,name,email', 'members', 'invitations' => fn ($q) => $q->pending()]);

        return Inertia::render('Organizations/Show', [
            'organization' => $organization,
        ]);
    }
}
