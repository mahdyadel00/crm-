<?php

/** --------------------------------------------------------------------------------
 * This middleware class validates input requests for the payments controller
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Requests\Payments;

use App\Rules\NoTags;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentStoreUpdate extends FormRequest {

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
            'payment_invoiceid.exists' => __('lang.invoice_not_found'),
        ];
    }

    /**
     * Validate the request
     * @return array
     */
    public function rules() {

        $rules = [];

        /**-------------------------------------------------------
         * [create] only rules
         * ------------------------------------------------------*/
        if ($this->getMethod() == 'POST') {
            $rules += [
                'payment_invoiceid' => [
                    'required',
                    Rule::exists('invoices', 'bill_invoiceid'),
                ],
            ];
        }

        /**-------------------------------------------------------
         * common rules for both [create] and [update] requests
        payment_transaction_id:

         * ------------------------------------------------------*/
        $rules += [
            'payment_gateway' => [
                'required',
                new NoTags,
            ],
            'payment_date' => [
                'required',
                'date',
            ],
            'payment_amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'payment_transaction_id' => [
                'nullable',
                new NoTags,
            ],
            'payment_notes' => [
                'nullable',
                new NoTags,
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
