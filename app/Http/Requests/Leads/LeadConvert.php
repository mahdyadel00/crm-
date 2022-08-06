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

class LeadConvert extends FormRequest {

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
        return [];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        /**-------------------------------------------------------
         * validation rules
         * ------------------------------------------------------*/
        $rules = [
            'first_name' => [
                'required',
                new NoTags,
            ],
            'last_name' => [
                'required',
                new NoTags,
            ],
            'email' => [
                'required',
                'email',
            ],
            'client_company_name' => [
                'required',
                new NoTags,
            ],
            'phone' => [
                'nullable',
                new NoTags,
            ],
            'street' => [
                'nullable',
                new NoTags,
            ],
            'city' => [
                'nullable',
                new NoTags,
            ],
            'state' => [
                'nullable',
                new NoTags,
            ],
            'zip' => [
                'nullable',
                new NoTags,
            ],
            'country' => [
                'nullable',
                new NoTags,
            ],
            'lead_website' => [
                'nullable',
                'url',
            ],
        ];;

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
