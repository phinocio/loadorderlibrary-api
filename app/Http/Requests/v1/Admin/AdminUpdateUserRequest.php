<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            ],
            'is_verified' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
