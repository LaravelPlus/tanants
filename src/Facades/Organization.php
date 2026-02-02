<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelPlus\Tenants\Services\OrganizationService;

final class Organization extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return OrganizationService::class;
    }
}
