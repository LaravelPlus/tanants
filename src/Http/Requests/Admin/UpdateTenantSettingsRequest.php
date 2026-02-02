<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTenantSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'entity_name' => ['required', 'string', 'max:50'],
            'entity_name_plural' => ['required', 'string', 'max:50'],
            'multi_org' => ['required', 'boolean'],
            'personal_org' => ['required', 'boolean'],
            'routing_mode' => ['required', 'string', 'in:session,url'],
            'url_prefix' => ['nullable', 'string', 'max:50'],
            'invitation_expiry_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'max_organizations_per_user' => ['required', 'integer', 'min:0'],
            'max_members_per_organization' => ['required', 'integer', 'min:0'],
            'default_member_role' => ['required', 'string', 'max:50'],
            'member_roles' => ['required', 'array', 'min:1'],
            'member_roles.*' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'entity_name.required' => 'The entity name is required.',
            'entity_name_plural.required' => 'The plural entity name is required.',
            'routing_mode.in' => 'The routing mode must be either session or url.',
            'invitation_expiry_hours.min' => 'The invitation expiry must be at least 1 hour.',
            'invitation_expiry_hours.max' => 'The invitation expiry cannot exceed 720 hours (30 days).',
            'member_roles.min' => 'At least one member role is required.',
            'default_member_role.required' => 'The default member role is required.',
        ];
    }

    /**
     * Additional validation: default_member_role must be in member_roles.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator): void {
            $memberRoles = $this->input('member_roles', []);
            $defaultRole = $this->input('default_member_role');

            if (is_array($memberRoles) && $defaultRole && !in_array($defaultRole, $memberRoles, true)) {
                $validator->errors()->add(
                    'default_member_role',
                    'The default member role must be one of the defined member roles.'
                );
            }
        });
    }
}
