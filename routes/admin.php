<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LaravelPlus\Tenants\Http\Controllers\Admin\MemberController;
use LaravelPlus\Tenants\Http\Controllers\Admin\OrganizationController;
use LaravelPlus\Tenants\Http\Controllers\Admin\TenantSettingsController;

$config = config('tenants.admin', []);
$prefix = $config['prefix'] ?? 'admin/organizations';
$middleware = $config['middleware'] ?? ['web', 'auth'];

Route::middleware($middleware)
    ->prefix($prefix)
    ->name('admin.organizations.')
    ->group(function (): void {
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('create', [OrganizationController::class, 'create'])->name('create');
        Route::post('/', [OrganizationController::class, 'store'])->name('store');

        // Tenant settings (before {organization} wildcard)
        Route::get('settings', [TenantSettingsController::class, 'index'])->name('settings');
        Route::patch('settings', [TenantSettingsController::class, 'update'])->name('settings.update');

        Route::get('{organization}', [OrganizationController::class, 'show'])->name('show');
        Route::get('{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
        Route::put('{organization}', [OrganizationController::class, 'update'])->name('update');
        Route::patch('{organization}', [OrganizationController::class, 'update']);
        Route::delete('{organization}', [OrganizationController::class, 'destroy'])->name('destroy');

        // Member management
        Route::post('{organization}/members', [MemberController::class, 'store'])->name('members.store');
        Route::delete('{organization}/members/{user}', [MemberController::class, 'destroy'])->name('members.destroy');
        Route::patch('{organization}/members/{user}/role', [MemberController::class, 'updateRole'])->name('members.update-role');
    });
