<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the leads controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Leads;

use App\Rules\NoTags;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadStoreUpdate extends FormRequest {

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
            'project_categoryid.exists' => __('lang.item_not_found'),
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
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        //validate
        $rules += [
            'lead_title' => [
                'required',
                new NoTags,
            ],
            'lead_firstname' => [
                'required',
                new NoTags,
            ],
            'lead_lastname' => [
                'required',
                new NoTags,
            ],
            'lead_email' => [
                'nullable',
                'email',
            ],
            'lead_value' => [
                'nullable',
                'numeric',
            ],
            'assigned' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        foreach ($value as $user_id) {
                            if (\App\Models\User::Where('id', $user_id)->where('type', 'team')->doesntExist()) {
                                return $fail(__('lang.assiged_user_not_found'));
                                break;
                            }
                        }
                    } else {
                        return $fail(__('lang.assigned') . ' - ' . __('lang.is_invalid'));
                    }
                },
            ],
            'lead_status' => [
                'required',
                Rule::exists('leads_status', 'leadstatus_id'),
            ],
            'lead_description' => [
                'nullable',
            ],
            'lead_source' => [
                'nullable',
                new NoTags,
            ],
            'lead_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
            'lead_last_contacted' => [
                'nullable',
                'date',
            ],
            'lead_company_name' => [
                'nullable',
                new NoTags,
            ],
            'lead_phone' => [
                'nullable',
                new NoTags,
            ],
            'lead_street' => [
                'nullable',
                new NoTags,
            ],
            'lead_city' => [
                'nullable',
                new NoTags,
            ],
            'lead_state' => [
                'nullable',
                new NoTags,
            ],
            'lead_zip' => [
                'nullable',
                new NoTags,
            ],
            'lead_country' => [
                'nullable',
                new NoTags,
            ],
            'lead_website' => [
                'nullable',
                'url',
            ],
            'edit_leads_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (\App\Models\Lead::Where('lead_id', $value)->doesntExist()) {
                            return $fail(__('lang.lead_not_found'));
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
