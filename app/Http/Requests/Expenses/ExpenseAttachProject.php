<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the expenses controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Expenses;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseAttachProject extends FormRequest {

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

        //initialize
        $rules = [];

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'expense_clientid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (!\App\Models\Client::find(request('expense_clientid'))) {
                            return $fail(__('lang.item_not_found'));
                        }
                    }
                },
            ],
            'expense_projectid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (!\App\Models\Project::find(request('expense_projectid'))) {
                            return $fail(__('lang.item_not_found'));
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
