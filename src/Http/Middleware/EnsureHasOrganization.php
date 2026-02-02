<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureHasOrganization
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->organizations()->count() === 0) {
            return redirect()->route('organizations.create')
                ->with('warning', 'Please create an organization to continue.');
        }

        return $next($request);
    }
}
