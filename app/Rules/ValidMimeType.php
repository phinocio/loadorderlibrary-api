<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidMimeType implements ValidationRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param string $attribute
	 * @param mixed $value
	 * @param Closure $fail
	 */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
		$validMimes = [
			'text/plain',
			'application/x-wine-extension-ini'
		];

//		dd($value);

//		$file = $value->getClientOriginalName();

		$mimetype = $value->getClientMimeType();

		if (!in_array($mimetype, $validMimes)) {
			$fail('The :value has an invalid mimetype. Detected mimetype is: ' . $mimetype);
		}
    }
}
