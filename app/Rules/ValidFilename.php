<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Helpers\ValidFiles;

class ValidFilename implements Rule
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

		return in_array(strtolower($this->file), ValidFiles::all());
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return $this->file . ' is not named correctly. Please double check valid filenames on the right and try again.';
	}
}
