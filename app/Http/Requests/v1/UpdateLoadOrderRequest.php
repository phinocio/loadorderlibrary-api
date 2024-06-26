<?php

namespace App\Http\Requests\v1;

use App\Rules\ValidFilename;
use App\Rules\ValidMimeType;
use App\Rules\ValidNumLines;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoadOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'string|nullable',
            'game' => 'required|exists:games,id',
            'version' => 'string|nullable|max:15',
            'website' => 'string|nullable',
            'discord' => 'string|nullable',
            'readme' => 'string|nullable',
            'files' => 'nullable',
            'files.*' => [new ValidMimetype(), 'max:512', new ValidNumLines(), new ValidFilename()],
            'expires' => 'string|nullable',
            'private' => 'string|nullable',
        ];
    }
}
