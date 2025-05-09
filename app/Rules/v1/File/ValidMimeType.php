<?php

declare(strict_types=1);

namespace App\Rules\v1\File;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\File;
use Illuminate\Translation\PotentiallyTranslatedString;

final class ValidMimeType implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $validMimes = [
            'text/plain',
            'application/x-wine-extension-ini',
            'application/octet-stream',
        ];

        /** @var File $value */
        $mimetype = mime_content_type($value->getRealPath());

        if (! in_array($mimetype, $validMimes)) {
            $fail('The :attribute has an invalid mimetype. Detected mimetype is: '.$mimetype);
        }
    }
}
