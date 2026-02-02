<?php

declare(strict_types=1);

namespace LaravelPlus\Tenants\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:organizations,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_personal' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The organization name is required.',
            'slug.unique' => 'This slug is already taken.',
        ];
    }
}
