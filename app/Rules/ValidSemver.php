<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidSemver implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		return preg_match('/(\d+)\.(\d+)\.(\d+)(-alpha|-beta)?/', $value);
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'The version format is not valid. Format is #.#.# with optional -alpha or -beta suffix, and # is any number.';
	}
}
