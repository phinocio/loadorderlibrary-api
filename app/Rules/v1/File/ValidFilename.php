<?php

declare(strict_types=1);

namespace App\Rules\v1\File;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
use Illuminate\Translation\PotentiallyTranslatedString;

final class ValidFilename implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            $fail('The :attribute must be a valid file upload.');

            return;
        }

        $file = $value->getClientOriginalName();

        if (! in_array(mb_strtolower($file), ValidFiles::all())) {
            $fail('The file is not named correctly.');
        }
    }
}
