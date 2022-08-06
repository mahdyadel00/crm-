<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the estimates controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Estimates;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EstimateSave extends FormRequest {

    /**
     * we are checking authorised users via the middleware
     * so just retun true here
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * custom error messages for specific valdation checks
     * @optional
     * @return array
     */
    public function messages() {
        //custom error messages
        return [];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules = [
            'bill_date' => [
                'required',
                'date',
            ],
            'bill_due_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('bill_date') != '' && (strtotime($value) < strtotime(request('bill_date')))) {
                        return $fail(__('lang.due_date_must_be_after_start_date'));
                    }
                },
            ],
            'js_item_description' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (hasHTML($data)) {
                            return $fail(__('lang.description_no_html'));
                        }
                    }
                },
            ],
            'js_item_quantity' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (!is_numeric($data) || (is_numeric($data) && $data <= 0)) {
                            return $fail(__('lang.quantity_is_invalid'));
                        }
                    }
                },
            ],
            'js_item_rate' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (!is_numeric($data) || (is_numeric($data) && $data <= 0)) {
                            return $fail(__('lang.rate_is_invalid'));
                        }
                    }
                },
            ],
            'js_item_total' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (hasHTML($data)) {
                            return $fail(__('lang.units_no_html'));
                        }
                    }
                },
            ],
            'js_item_unit' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (hasHTML($data)) {
                            return $fail(__('lang.units_no_html'));
                        }
                    }
                },
            ],
            'bill_subtotal' => [
                'required',
                'numeric',
            ],
            'bill_amount_before_tax' => [
                'required',
                'numeric',
            ],
            'bill_final_amount' => [
                'required',
                'numeric',
            ],
            'bill_tax_total_percentage' => [
                'required',
                'numeric',
            ],
            'bill_tax_total_amount' => [
                'required',
                'numeric',
            ],
            'bill_discount_percentage' => [
                'required',
                'numeric',
            ],
            'bill_discount_amount' => [
                'required',
                'numeric',
            ],
        ];
        //validate
        return $rules;
    }

    /**
     * Deal with the errors - send messages to the frontend
     */
    public function failedValidation(Validator $validator) {

        $errors = $validator->errors();
        $messages = '';
        foreach ($errors->all() as $message) {
            $messages .= "<li>$message</li>";
        }

        abort(409, $messages);
    }
}
