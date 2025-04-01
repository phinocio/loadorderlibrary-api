<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/** @mixin \App\Models\User */
final class UpdateUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bio' => ['sometimes', 'present', 'nullable', 'string', 'max:2048'],
            'discord' => ['sometimes', 'present', 'nullable', 'string', 'max:255'],
            'kofi' => ['sometimes', 'present', 'nullable', 'string', 'max:255'],
            'patreon' => ['sometimes', 'present', 'nullable', 'string', 'max:255'],
            'website' => ['sometimes', 'present', 'nullable', 'string', 'max:255'],
        ];
    }
}
