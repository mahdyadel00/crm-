<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the tickets controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Tickets;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NoTags;


class TicketStoreUpdate extends FormRequest {

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
        return [
            'ticket_clientid.exists' => __('lang.client_not_found'),
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
                'ticket_clientid' => [
                    'required',
                    Rule::exists('clients', 'client_id'),
                ],
                'ticket_projectid' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value != '') {
                            if (!\App\Models\Project::where('project_clientid', request('ticket_clientid'))->find(request('ticket_projectid'))) {
                                return $fail(__('lang.project_not_found'));
                            }
                        }
                    },
                ],
            ];
        }

        /**-------------------------------------------------------
         * [update] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'PUT') {
            $rules += [
                'ticket_projectid' => [
                    'nullable',
                    Rule::exists('projects', 'project_id'),
                ],
            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
         * ------------------------------------------------------*/
        $rules += [
            'ticket_subject' => [
                'required',
                new NoTags,
            ],
            'ticket_message' => [
                'required'
            ],
            'ticket_priority' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['normal', 'high', 'urgent'])) {
                        return $fail(__('lang.invalid_ticket_priority'));
                    }
                },
            ],
            'ticket_categoryid' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (!\App\Models\Category::where('category_type', 'ticket')->find(request('ticket_categoryid'))) {
                            return $fail(__('lang.invalid_department'));
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
