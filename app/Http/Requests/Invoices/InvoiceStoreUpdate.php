<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the invoices controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Invoices;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceStoreUpdate extends FormRequest {

    //use App\Http\Requests\TemplateValidation;
    //function update(TemplateValidation $request,

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
            'bill_categoryid.exists' => __('lang.item_not_found'),
            'bill_projectid.exists' => __('lang.item_not_found'),
            'bill_recurring_duration.required_if' => __('lang.fill_in_all_required_fields'),
            'company_name.required' => __('lang.company_name') . ' ' . __('lang.is_required'),
            'first_name.required' => __('lang.first_name') . ' ' . __('lang.is_required'),
            'last_name.required' => __('lang.last_name') . ' ' . __('lang.required'),
            'email.required' => __('lang.email') . ' ' . __('lang.required'),
            'email.email' => __('lang.email') . ' ' . __('lang.is_not_a_valid_email_address'),
            'email.unique' => __('lang.email_already_exists'),
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
                'bill_projectid' => [
                    'nullable',
                    Rule::exists('projects', 'project_id'),
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
         * [update] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'PUT') {
            $rules += [

            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'bill_date' => [
                'required',
                'date',
            ],
            'bill_due_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('bill_date') != '' && (strtotime($value) < strtotime(request('bill_date')))) {
                        return $fail(__('lang.due_date_must_be_after_start_date'));
                    }
                },
            ],
            'bill_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
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
