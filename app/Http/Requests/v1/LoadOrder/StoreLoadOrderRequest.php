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
        return true; // Authorization is handled by the policy
    }

    /** @return array<string, array<int, string|ValidMimeType|ValidFilename>> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'version' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'discord' => ['nullable', 'url', 'max:255'],
            'readme' => ['nullable', 'url', 'max:255'],
            'is_private' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
            'game_id' => ['required', 'exists:games,id'],
            'files' => ['required'],
            'files.*' => ['max:512', new ValidMimeType, new ValidFilename],
        ];
    }
}
