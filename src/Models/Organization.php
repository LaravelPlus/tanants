<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use LaravelPlus\Tenants\Database\Factories\OrganizationFactory;

final class Organization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'is_personal',
        'logo_path',
        'metadata',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(OrganizationInvitation::class);
    }

    public function isPersonal(): bool
    {
        return $this->is_personal;
    }

    public function isOwnedBy($user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function hasMember($user): bool
    {
        return $this->members()->where('users.id', $user->id)->exists();
    }

    public function getMemberRole($user): ?string
    {
        $member = $this->members()->where('users.id', $user->id)->first();

        return $member?->pivot->role;
    }

    public function addMember($user, string $role = 'member'): void
    {
        $this->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
        ]);
    }

    public function removeMember($user): void
    {
        $this->members()->detach($user->id);
    }

    public function changeMemberRole($user, string $role): void
    {
        $this->members()->updateExistingPivot($user->id, [
            'role' => $role,
        ]);
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Organization $organization): void {
            if (empty($organization->uuid)) {
                $organization->uuid = (string) Str::uuid();
            }

            if (empty($organization->slug)) {
                $organization->slug = self::generateUniqueSlug(Str::slug($organization->name));
            }
        });
    }

    /**
     * Generate a unique slug, appending a counter if necessary.
     */
    private static function generateUniqueSlug(string $slug): string
    {
        $originalSlug = $slug;
        $counter = 1;

        while (self::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    protected static function newFactory(): OrganizationFactory
    {
        return OrganizationFactory::new();
    }
}
