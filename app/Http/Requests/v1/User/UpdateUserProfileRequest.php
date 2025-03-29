<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

/** @mixin \App\Models\User */
class UpdateUserProfileRequest extends FormRequest
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
            'bio' => 'nullable|string',
            'discord' => 'nullable|string',
            'kofi' => 'nullable|string',
            'patreon' => 'nullable|string',
            'website' => 'nullable|string',
        ];
    }
}
