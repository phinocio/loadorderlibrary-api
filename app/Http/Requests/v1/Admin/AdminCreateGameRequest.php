<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class AdminCreateGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /** @return array<string, array<string>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
