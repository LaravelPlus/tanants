<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use LaravelPlus\Tenants\Http\Requests\StoreInvitationRequest;
use LaravelPlus\Tenants\Models\Organization;
use LaravelPlus\Tenants\Models\OrganizationInvitation;
use LaravelPlus\Tenants\Services\InvitationService;

final class InvitationController
{
    public function __construct(
        private(set) InvitationService $invitationService,
    ) {}

    public function show(string $token): Response|RedirectResponse
    {
        $invitation = OrganizationInvitation::where('token', $token)
            ->with('organization')
            ->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('dashboard')
                ->with('error', 'This invitation is no longer valid.');
        }

        return Inertia::render('Invitations/Show', [
            'invitation' => [
                'token' => $invitation->token,
                'email' => $invitation->email,
                'role' => $invitation->role,
                'organization' => [
                    'name' => $invitation->organization->name,
                ],
                'expires_at' => $invitation->expires_at->toISOString(),
            ],
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        $invitation = OrganizationInvitation::where('token', $token)->firstOrFail();

        $this->invitationService->accept($invitation);

        return redirect()->route('dashboard')
            ->with('status', 'You have joined '.$invitation->organization->name.'.');
    }

    public function decline(string $token): RedirectResponse
    {
        $invitation = OrganizationInvitation::where('token', $token)->firstOrFail();

        $this->invitationService->decline($invitation);

        return redirect()->route('dashboard')
            ->with('status', 'Invitation declined.');
    }

    public function store(StoreInvitationRequest $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validated();

        $this->invitationService->send(
            $organization,
            $validated['email'],
            $validated['role'] ?? config('tenants.default_member_role', 'member'),
            $request->user()->id,
        );

        return back()->with('status', 'Invitation sent successfully.');
    }

    public function destroy(Organization $organization, OrganizationInvitation $invitation): RedirectResponse
    {
        $this->invitationService->cancel($invitation);

        return back()->with('status', 'Invitation cancelled.');
    }
}
