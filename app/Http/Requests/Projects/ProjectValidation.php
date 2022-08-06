<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the projects controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Projects;

use App\Rules\NoTags;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectValidation extends FormRequest {

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
         * [create] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST' && request('client-selection-type') == 'existing') {
            $rules += [
                'project_clientid' => [
                    'required',
                    Rule::exists('clients', 'client_id')],
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
         * ------------------------------------------------------*/
        $rules += [
            'project_title' => [
                'required',
                new NoTags,
            ],
            'project_date_start' => [
                'required',
                'date',
            ],
            'project_date_due' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('project_date_start') != '' && (strtotime($value) < strtotime(request('project_date_start')))) {
                        return $fail(__('lang.due_date_must_be_after_start_date'));
                    }
                },
            ],
            'project_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
            'manager' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (\App\Models\User::Where('id', $value)->Where('type', 'team')->doesntExist()) {
                            return $fail(__('lang.assiged_manager_not_found'));
                        }
                    }
                },
            ],
            'assigned' => [
                'nullable',
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
            'project_billing_rate' => [
                'nullable',
                'numeric',
            ],
            'project_billing_type' => [
                Rule::in(['hourly', 'fixed']),
            ],
            'project_billing_estimated_hours' => [
                'nullable',
                'numeric',
            ],
            'project_billing_costs_estimate' => [
                'nullable',
                'numeric',
            ],
            'assignedperm_tasks_collaborate' => [
                Rule::in(['on', null]),
            ],
            'clientperm_tasks_view' => [
                Rule::in(['on', null]),
            ],
            'clientperm_tasks_collaborate' => [
                Rule::in(['on', null]),
            ],
            'clientperm_tasks_create' => [
                Rule::in(['on', null]),
            ],
            'clientperm_timesheets_view' => [
                Rule::in(['on', null]),
            ],
            'clientperm_expenses_view' => [
                Rule::in(['on', null]),
            ],
            'project_progress_manually' => [
                Rule::in(['on', null]),
            ],
        ];

        //validate
        return $rules;
    }

    /**
     * Custom error handing - show message to front end
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
