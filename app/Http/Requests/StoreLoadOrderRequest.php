<?php

namespace App\Http\Requests;

use App\Rules\ValidFilename;
use App\Rules\ValidMimeType;
use App\Rules\ValidNumLines;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLoadOrderRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'string|nullable',
            'game' => 'required',
            'version' => ['string', 'nullable', 'max:15'],
            'website' => 'string|nullable',
            'discord' => 'string|nullable',
            'readme' => 'string|nullable',
            'files' => 'required',
            'files.*' => [new ValidMimetype(), 'max:512', new ValidNumLines(), new ValidFilename()],
            'expires' => 'string|nullable',
            'private' => 'string|nullable',
        ];
    }
}
