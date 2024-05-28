<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidNumLines implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! count(explode("\n", file_get_contents($value))) >= 1) {
            $fail(':value is not valid. If you believe this is wrong, contact Phinocio.');
        }
    }
}
