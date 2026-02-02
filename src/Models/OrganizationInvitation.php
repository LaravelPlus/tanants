<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use LaravelPlus\Tenants\Database\Factories\OrganizationInvitationFactory;

final class OrganizationInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'organization_id',
        'email',
        'role',
        'token',
        'invited_by',
        'accepted_at',
        'declined_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'declined_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isPending(): bool
    {
        return !$this->accepted_at && !$this->declined_at && !$this->isExpired();
    }

    public function accept(): void
    {
        $this->accepted_at = now();
        $this->save();
    }

    public function decline(): void
    {
        $this->declined_at = now();
        $this->save();
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->where('expires_at', '>', now());
    }

    public function scopeExpired(Builder $query): void
    {
        $query->where('expires_at', '<', now())
            ->whereNull('accepted_at');
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (OrganizationInvitation $invitation): void {
            if (empty($invitation->uuid)) {
                $invitation->uuid = (string) Str::uuid();
            }

            if (empty($invitation->token)) {
                $invitation->token = Str::random(40);
            }
        });
    }

    protected static function newFactory(): OrganizationInvitationFactory
    {
        return OrganizationInvitationFactory::new();
    }
}
