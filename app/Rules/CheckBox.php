<?php

/** --------------------------------------------------------------------------
 * GrowCRM
 * Custom validation rule to prevent users entering HTML
 * @source Growcr.io
 *--------------------------------------------------------------------------*/
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckBox implements Rule {
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Determine if the validation rule passes. Input with any HTML fails
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value) {
        //only null or 'on'
        if ($value == '' || $value  == 'on') {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return ':attribute ' . __('lang.is_invalid');
    }
}
