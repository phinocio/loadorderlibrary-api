<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\LoadOrder;

use App\Rules\v1\File\ValidFilename;
use App\Rules\v1\File\ValidMimeType;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateLoadOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'version' => ['sometimes', 'nullable', 'string'],
            'website' => ['sometimes', 'nullable', 'url'],
            'discord' => ['sometimes', 'nullable', 'url'],
            'readme' => ['sometimes', 'nullable', 'url'],
            'is_private' => ['sometimes', 'boolean'],
            'expires_at' => ['sometimes', 'nullable', 'date'],
            'game_id' => ['sometimes', 'exists:games,id'],
            'files' => ['sometimes', 'array'],
            'files.*' => ['max:512', new ValidMimeType, new ValidFilename],
        ];
    }
}
