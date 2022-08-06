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
use Illuminate\Validation\Rule;

class EstimateStoreUpdate extends FormRequest {

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
        return [
            'bill_clientid.exists' => __('lang.item_not_found'),
            'bill_projectid.exists' => __('lang.item_not_found'),
            'estimate_recurring_duration.required_if' => __('lang.fill_in_all_required_fields'),
        ];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        //initialize
        $rules = [];

        /**-------------------------------------------------------
         * [create][existing client] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST' && request('client-selection-type') == 'existing') {
            $rules += [
                'bill_clientid' => [
                    'required',
                    Rule::exists('clients', 'client_id'),
                ],
            ];
        }

        /**-------------------------------------------------------
         * [create][new client] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST' && request('client-selection-type') == 'new') {
            $rules += [
                'client_company_name' => [
                    'required',
                ],
                'first_name' => [
                    'required',
                ],
                'last_name' => [
                    'required',
                ],
                'email' => [
                    'required',
                    'email',
                    'unique:users,email',
                ],
            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
        tags[]: hello
        bill_notes: <p>Testing</p>
        bill_terms:
         * ------------------------------------------------------*/
        $rules += [
            'bill_date' => [
                'required',
                'date',
            ],
            'bill_expiry_date' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('bill_date') != '' && (strtotime($value) < strtotime(request('bill_date')))) {
                        return $fail(__('lang.expiry_date_must_be_after_estimate_date'));
                    }
                },
            ],
            'bill_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
            'bill_projectid' => [
                'nullable',
                Rule::exists('projects', 'project_id'),
            ],
            'tags' => [
                'bail',
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    foreach ($value as $key => $data) {
                        if (hasHTML($data)) {
                            return $fail(__('lang.tags_no_html'));
                        }
                    }
                },
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
