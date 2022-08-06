<?php

/** --------------------------------------------------------------------------
 * GrowCRM
 * Custom validation rule to prevent users entering HTML
 * @source Growcr.io
 *--------------------------------------------------------------------------*/
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoTags implements Rule {
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

        //post arrays - check each item
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                if ($val == strip_tags($val)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        //run strip tags and then compare the strings
        if ($value == strip_tags($value)) {
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
        return ':attribute ' . __('lang.must_not_contain_any_html');
    }
}
