<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the projects controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Subscriptions;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionValidation extends FormRequest {

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
            'subscription_clientid.required' => __('lang.client_is_required'),
            'subscription_clientid.exists' => __('lang.client_not_found'),
            'subscription_categoryid.exists' => __('lang.category_not_found'),
            'subscription_gateway_product.required' => __('lang.subscription_product'),
            'subscription_gateway_price.required' => __('lang.subscription_price'),
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
         * common rules
         * ------------------------------------------------------*/
        $rules += [
            'subscription_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
            'subscription_clientid' => [
                'required',
                Rule::exists('clients', 'client_id'),
            ],
            'subscription_projectid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (!\App\Models\Project::where('project_clientid', request('subscription_clientid'))->find(request('subscription_projectid'))) {
                            return $fail(__('lang.project_not_found'));
                        }
                    }
                },
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
