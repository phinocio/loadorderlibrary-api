<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMimetype implements Rule
{

	protected $file = '';
	protected $mimetype = '';

	protected $validMimes = [
		'text/plain',
		'application/x-wine-extension-ini'
	];
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

		$this->mimetype = mime_content_type($value->getRealPath());

		return in_array($this->mimetype, $this->validMimes);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->file . ' has an invalid mimetype. Detected mimetype is: ' . $this->mimetype;
    }
}
