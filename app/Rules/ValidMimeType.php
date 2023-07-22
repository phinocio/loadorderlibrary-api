<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\File;

class ValidMimeType implements ValidationRule
{
    /**
     * Run the validation rule.
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validMimes = [
            'text/plain',
            'application/x-wine-extension-ini',
        ];//

        /** @noinspection PhpComposerExtensionStubsInspection */
        /** @var File $value */
        $mimetype = mime_content_type($value->getRealPath());

        if (! in_array($mimetype, $validMimes)) {
            $fail('The :value has an invalid mimetype. Detected mimetype is: '.$mimetype);
        }
    }
}
