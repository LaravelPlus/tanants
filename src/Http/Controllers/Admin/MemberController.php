<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use LaravelPlus\Tenants\Contracts\OrganizationServiceInterface;
use LaravelPlus\Tenants\Models\Organization;

final class MemberController
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

    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role' => ['required', 'string', 'in:'.implode(',', config('tenants.member_roles', ['owner', 'admin', 'member']))],
        ]);

        $this->organizationService->addMember(
            $organization,
            (int) $validated['user_id'],
            $validated['role'],
        );

        return back()->with('status', 'Member added successfully.');
    }

    public function destroy(Organization $organization, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $this->organizationService->removeMember($organization, $user->id);

        return back()->with('status', 'Member removed successfully.');
    }

    public function updateRole(Request $request, Organization $organization, User $user): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', config('tenants.member_roles', ['owner', 'admin', 'member']))],
        ]);

        $this->organizationService->changeMemberRole($organization, $user->id, $validated['role']);

        return back()->with('status', 'Member role updated successfully.');
    }
}
