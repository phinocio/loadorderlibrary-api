<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidNumLines implements Rule
{

	protected $file = '';
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
		$this->file = $value->getClientOriginalName();
		return count(explode("\n", file_get_contents($value))) >= 1;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return $this->file . ' is not valid. If you believe this is wrong, contact Phin.';
	}
}
