<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use LaravelPlus\Tenants\Http\Requests\Admin\UpdateTenantSettingsRequest;
use LaravelPlus\Tenants\Services\TenantSettingsService;

final class TenantSettingsController
{
    public function __construct(
        private(set) TenantSettingsService $settingsService,
    ) {}

    private function authorizeAdmin(): void
    {
        $user = auth()->user();

        if (!$user || !array_any(['super-admin', 'admin'], fn (string $role): bool => $user->hasRole($role))) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    public function index(): Response
    {
        $this->authorizeAdmin();

        return Inertia::render('admin/Organizations/Settings', [
            'settings' => $this->settingsService->getAll(),
        ]);
    }

    public function update(UpdateTenantSettingsRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $this->settingsService->update($request->validated());

        if (class_exists(\App\Models\AuditLog::class)) {
            \App\Models\AuditLog::log('tenant_settings.updated', null, null, $request->validated());
        }

        return redirect()->route('admin.organizations.settings')
            ->with('status', 'Tenant settings updated successfully.');
    }
}
