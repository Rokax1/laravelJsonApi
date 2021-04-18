<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Slug implements Rule
{

    private $message;
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

        //que no tenga guines bajos
        if (preg_match('/_/',$value)) {
            $this->message= trans('validation.no_underscores');
            return false;
        }
        //que no inicio guiones
        if (preg_match('/^-/',$value)) {
            $this->message= trans('validation.no_starting_dashes');
            return false;
        }
        //que no termine con guiones
        if (preg_match('/-$/',$value)) {
            $this->message= trans('validation.no_ending_dashes');
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
