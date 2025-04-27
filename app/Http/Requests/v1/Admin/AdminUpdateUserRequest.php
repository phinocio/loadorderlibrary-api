<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** @mixin \App\Models\User */
final class AdminUpdateUserRequest extends FormRequest
{
    /** Determine if the user is authorized to make this request. */
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

            'email' => [
                'sometimes',
                'present',
                'nullable',
                'email',
                'max:255',
                // @phpstan-ignore property.nonObject
                Rule::unique('users')->ignore($this->route('user')->id),
            ],
            'is_verified' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
