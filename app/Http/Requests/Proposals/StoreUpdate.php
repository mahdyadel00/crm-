<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the template controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Proposals;

use App\Rules\NoTags;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdate extends FormRequest {

    //use App\Http\Requests\Foo\StoreUpdate;
    //function update(StoreUpdate $request,

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
        return [
            'doc_client_id.exists' => __('lang.client') . ' - ' . __('lang.could_not_be_found'),
            'doc_lead_id.exists' => __('lang.lead') . ' - ' . __('lang.could_not_be_found'),
            'doc_date_start.required' => __('lang.proposal_date') . ' - ' . __('lang.is_required'),
            'doc_title.required' => __('lang.proposal_title') . ' - ' . __('lang.is_required'),
            'doc_categoryid.required' => __('lang.category') . ' - ' . __('lang.is_required'),
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
         * [create] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST') {
            $rules += [
                'doc_client_id' => [
                    'nullable',
                    new NoTags,
                    Rule::exists('clients', 'client_id'),
                    function ($attribute, $value, $fail) {
                        if ($value == '' && request('doc_lead_id') == '') {
                            return $fail(__('lang.client_or_lead_required'));
                        }
                    },
                ],
                'doc_lead_id' => [
                    'nullable',
                    new NoTags,
                    Rule::exists('clients', 'client_id'),
                    function ($attribute, $value, $fail) {
                        if ($value == '' && request('doc_client_id') == '') {
                            return $fail(__('lang.client_or_lead_required'));
                        }
                    },
                ],
            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'doc_date_start' => [
                'required',
                'date',
            ],
            'doc_date_end' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('doc_date_start') != '' && (strtotime($value) < strtotime(request('doc_date_start')))) {
                        return $fail(__('lang.proposal_valid_to_date_error'));
                    }
                },
            ],
            'doc_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
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