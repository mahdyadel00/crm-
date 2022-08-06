<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for razorpay payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Illuminate\Support\Str;
use Log;
use Razorpay\Api\Api;

class RazorpayPaymentRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /** ----------------------------------------------------
     * [onetime payment]
     * Start the process for a single razorpay payment
     * @param array $data information payload
     * @return int session id
     * ---------------------------------------------------*/
    public function onetimePayment($data = []) {

        //validate
        if (!is_array($data)) {
            Log::error("invalid paymment payload data", ['process' => '[razorpay-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        // Generate random transaction id
        $receipt_id = Str::random(20);

        // Create a new order with razorpay
        $api = new Api(config('system.settings_razorpay_keyid'), config('system.settings_razorpay_secretkey'));

        // Creating order with razor pay (just like a stripe session)
        try {
            $order = $api->order->create([
                'receipt' => $receipt_id,
                'amount' => $data['unit_amount'],
                'currency' => $data['currency'],
            ]);
        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            Log::error("Razorpay Error - " . $e->getMessage(), ['process' => '[razorpay-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error("Razorpay Error - " . $e->getMessage(), ['process' => '[razorpay-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save session id in sessions database
        $payment_session = new \App\Models\PaymentSession();
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'razorpay';
        $payment_session->session_gateway_ref = $order['id']; //razopay order id
        $payment_session->session_amount = $data['amount'];
        $payment_session->session_invoices = $data['invoice_id'];
        $payment_session->save();

        //return the razorpay order id
        return $order['id'];

    }

    /** ----------------------------------------------------
     * [verify]
     * This checks if the post recieved on the thank you page
     * really came from Razorpay
     * @param array $data information payload
     * @return int session id
     * ---------------------------------------------------*/
    public function verifyTransaction() {

        //validate
        if (!request()->filled('razorpay_payment_id') || !request()->filled('razorpay_signature') || !request()->filled('razorpay_order_id')) {
            Log::error("error processing razorpay payment. missing data from razorpay", ['process' => '[payment-processing]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
            abort(409, __('lang.error_request_could_not_be_completed') . '. ' . __('lang.please_contact_support'));
        }

        //transaction data
        $data = [
            'razorpay_signature' => request('razorpay_signature'),
            'razorpay_payment_id' => request('razorpay_payment_id'),
            'razorpay_order_id' => request('razorpay_order_id'),
        ];

        //validate, by trying to retrieve the payment from Razorpay
        try {
            $api = new Api(config('system.settings_razorpay_keyid'), config('system.settings_razorpay_secretkey'));
            $order = $api->utility->verifyPaymentSignature($data);
            return true;
        } catch (\Exception $e) {
            //if payment is not found, it throws an exception.
            return false;
        }
    }

    /** ----------------------------------------------------
     * [register the payment]
     *   - create a web hook
     *   - this is an instant payment notification (what if user closes the browser quickly?)
     * really came from Razorpay
     * @param array $data information payload
     * @return int session id
     * ---------------------------------------------------*/
    public function registerPayment() {

        //transaction data
        $data = [
            'razorpay_signature' => request('razorpay_signature'),
            'razorpay_payment_id' => request('razorpay_payment_id'),
            'razorpay_order_id' => request('razorpay_order_id'),
        ];

        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'razorpay';
        $webhook->webhooks_type = 'razorpay-instant-notification';
        $webhook->webhooks_payment_type = 'onetime';
        $webhook->webhooks_payment_amount = null;
        $webhook->webhooks_payment_transactionid = $data['razorpay_payment_id'];
        $webhook->webhooks_matching_reference = $data['razorpay_order_id'];
        $webhook->webhooks_payload = json_encode($data);
        $webhook->webhooks_status = 'new';
        $webhook->save();

    }

}
