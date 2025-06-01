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
     * @return array<string, array<int, string|ValidMimeType|ValidFilename>>
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
            'private' => ['sometimes', 'boolean'],
            'expires' => ['sometimes', 'nullable', 'string'],
            'game' => ['sometimes', 'exists:games,id'],
            'files' => ['sometimes', 'array'],
            'files.*' => ['max:512', new ValidMimeType, new ValidFilename],
        ];
    }
}
