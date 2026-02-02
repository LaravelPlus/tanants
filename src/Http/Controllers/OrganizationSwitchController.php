<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LaravelPlus\Tenants\Contracts\OrganizationServiceInterface;
use LaravelPlus\Tenants\Models\Organization;

final class OrganizationSwitchController
{
    public function __construct(
        private(set) OrganizationServiceInterface $organizationService,
    ) {}

    public function __invoke(Request $request, Organization $organization): RedirectResponse
    {
        $this->organizationService->switchOrganization($request->user()->id, $organization);

        return redirect()->back()->with('status', 'Switched to '.$organization->name.'.');
    }
}
