<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the tasks controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Tasks;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TaskRecurrringSettings extends FormRequest {

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
            'task_recurring_duration.required' => __('lang.repeat_every').' - '.__('lang.is_required'),
            'task_recurring_duration.integer' => __('lang.item_not_found').' - '.__('lang.is_invalid'),
            'task_recurring_cycles.required' => __('lang.cycles').' - '.__('lang.is_required'),
            'task_recurring_next.required' => __('lang.first_task_date').' - '.__('lang.is_required'),
        ];
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
            'task_recurring_duration' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value <= 0) {
                        return $fail(__('lang.repeat_value_greater_than_zero'));
                    }
                },
            ],
            'task_recurring_period' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['day', 'week', 'month', 'year'])) {
                        return $fail(__('lang.invalid_repeat_every'));
                    }
                },
            ],
            'task_recurring_cycles' => [
                'required',
                'integer',
            ],
            'task_recurring_next' => [
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime(now()->toDateString())) {
                        return $fail(__('lang.first_task_date_cannot_be_in_past'));
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
