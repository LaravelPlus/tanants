<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Services;

use LaravelPlus\GlobalSettings\Models\Setting;
use RuntimeException;

final class TenantSettingsService
{
    /**
     * Config keys managed by this service and their types.
     *
     * @var array<string, string>
     */
    private const CONFIG_KEYS = [
        'entity_name' => 'string',
        'entity_name_plural' => 'string',
        'multi_org' => 'boolean',
        'personal_org' => 'boolean',
        'routing_mode' => 'string',
        'url_prefix' => 'string',
        'invitation_expiry_hours' => 'integer',
        'max_organizations_per_user' => 'integer',
        'max_members_per_organization' => 'integer',
        'default_member_role' => 'string',
        'member_roles' => 'array',
    ];

    /**
     * Get all tenant settings with GlobalSettings values taking priority over config defaults.
     *
     * @return array<string, mixed>
     */
    public function getAll(): array
    {
        $settings = [];

        foreach (self::CONFIG_KEYS as $key => $type) {
            $settings[$key] = $this->getValue($key, $type);
        }

        return $settings;
    }

    /**
     * Update tenant settings via GlobalSettings.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(array $data): void
    {
        if (!class_exists(Setting::class)) {
            throw new RuntimeException('GlobalSettings package is not installed. Settings cannot be updated.');
        }

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, self::CONFIG_KEYS)) {
                continue;
            }

            $storedValue = match (self::CONFIG_KEYS[$key]) {
                'boolean' => $value ? '1' : '0',
                'array' => is_array($value) ? json_encode($value) : (string) $value,
                'integer' => (string) (int) $value,
                default => (string) $value,
            };

            Setting::set("tenant.{$key}", $storedValue);
        }
    }

    /**
     * Get a single setting value with type coercion.
     */
    private function getValue(string $key, string $type): mixed
    {
        $settingValue = null;

        if (class_exists(Setting::class)) {
            $settingValue = Setting::get("tenant.{$key}");
        }

        if ($settingValue !== null) {
            return $this->castValue($settingValue, $type);
        }

        return config("tenants.{$key}");
    }

    /**
     * Cast a stored string value to the appropriate PHP type.
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => in_array($value, ['1', 'true', true, 1], true),
            'integer' => (int) $value,
            'array' => is_array($value) ? $value : (is_string($value) ? (json_decode($value, true) ?? []) : []),
            default => $value,
        };
    }
}
