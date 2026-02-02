<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

final class OrganizationMember extends Pivot
{
    protected $table = 'organization_members';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
