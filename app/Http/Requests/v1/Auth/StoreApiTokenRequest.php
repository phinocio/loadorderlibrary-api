<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreApiTokenRequest extends FormRequest
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
            'token_name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array'],
            'abilities.*' => ['required', 'string', 'in:create,read,update,delete'],
            'expires' => ['nullable', 'string'],
        ];
    }
}
