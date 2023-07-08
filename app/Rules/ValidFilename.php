<?php

namespace App\Rules;

use App\Helpers\ValidFiles;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFilename implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $file = $value->getClientOriginalName();

        if (! in_array(strtolower($file), ValidFiles::all())) {
            $fail('The :value is not named correctly.');
        }
    }
}
