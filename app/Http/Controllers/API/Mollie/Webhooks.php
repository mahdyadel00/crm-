<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for ipn calls from mollie
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\API\Mollie;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Log;
use \Mollie\Api;

class Webhooks extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        $this->middleware('guest');

    }

    /**
     * - receive the mollie IPN call
     * - make a request back to mollie to get the transaction details (security)
     * @return null
     */
    public function index() {

        Log::info("received a new webhook call from Mollie", ['process' => '[mollie-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);

        //validate that we have a valid request
        if (!request('id')) {
            Log::info("webhook is not want we expected - will ignore", ['process' => '[mollie-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);
        }

        //connect to mollie
        try {
            $mollie = new Api\MollieApiClient();
            $mollie->setApiKey($this->getKey());
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error("mollie webhook error - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, 'Webhook Error - ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error("mollie webhook error - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, 'Webhook Error - ' . $e->getMessage());
        }

        //get the payment actual payment from mollie
        try {
            $transaction_id = request('id');
            $payment = $mollie->payments->get($transaction_id);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error("mollie webhook error - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, 'Webhook Error - ' . $e->getMessage());
        } catch (Exception $e) {
            Log::error("mollie webhook error - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, 'Webhook Error - ' . $e->getMessage());
        }

        //make sure we do not already recorded this payment
        if (\App\Models\Payment::Where('payment_transaction_id', $transaction_id)->exists()) {
            Log::info("this transaction ($transaction_id) has already been recorded - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, "Webhook Error - This transaction ($transaction_id) has already been recorded");
        }

        //make sure we do not already have this queued for processing
        if (\App\Models\Webhook::Where('webhooks_payment_transactionid', $transaction_id)->exists()) {
            Log::info("this transaction ($transaction_id) is already queued for processing - " . $e->getMessage(), ['process' => '[mollie-webhook]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            abort(409, "Webhook Error - This transaction ($transaction_id) is already queued for processing");
        }


        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'mollie';
        $webhook->webhooks_type = 'payment_completed';
        $webhook->webhooks_payment_type = 'onetime';
        $webhook->webhooks_payment_amount = $payment->amount->value;
        $webhook->webhooks_payment_transactionid = $transaction_id;
        $webhook->webhooks_matching_reference = $transaction_id;
        $webhook->webhooks_payload = json_encode($payment);
        $webhook->webhooks_status = 'new';
        $webhook->save();

    }

    /** ----------------------------------------------------
     * get the right key for live or sandbox mode
     * @return string api key
     * ---------------------------------------------------*/
    public function getKey() {

        if (config('system.settings_mollie_mode') == 'live') {
            return config('system.settings_mollie_live_api_key');
        } else {
            return config('system.settings_mollie_test_api_key');
        }
    }

}