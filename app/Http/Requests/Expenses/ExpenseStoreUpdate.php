<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the expenses controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Expenses;

use App\Rules\NoTags;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ExpenseStoreUpdate extends FormRequest {

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
            'expense_categoryid.exists' => __('lang.item_not_found'),
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
            'expense_description' => [
                'required',
                new NoTags,
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'expense_amount' => [
                'required',
                'numeric',
            ],
            'expense_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
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
            'expense_billable' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value == 'on' && (!request()->filled('expense_projectid'))) {
                        return $fail(__('lang.a_project_is_required_for_billable_expenses'));
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
