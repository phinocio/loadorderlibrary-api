<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\LoadOrder;

use App\Rules\v1\File\ValidFilename;
use App\Rules\v1\File\ValidMimeType;
use Illuminate\Foundation\Http\FormRequest;

final class StoreLoadOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string|ValidMimeType|ValidFilename>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'version' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'discord' => ['nullable', 'url', 'max:255'],
            'readme' => ['nullable', 'url', 'max:255'],
            'private' => ['boolean'],
            'expires' => ['nullable', 'string'],
            'game' => ['required', 'exists:games,id'],
            'files' => ['required', 'array'],
            'files.*' => ['max:512', new ValidMimeType, new ValidFilename],
        ];
    }
}
