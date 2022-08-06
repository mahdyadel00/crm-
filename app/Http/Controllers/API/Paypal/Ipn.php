<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for ipn calls from paypal
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\API\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Log;

class Ipn extends Controller {

    //paypal ip url (live or sandbox)
    var $ipn_url = '';

    public function __construct() {

        //parent
        parent::__construct();

        $this->middleware('guest');

        //set paypal url
        if (config('system.settings_paypal_mode') == 'sandbox') {
            $this->ipn_url = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $this->ipn_url = 'https://ipnpb.paypal.com/cgi-bin/webscr';
        }

    }

    /**
     * Receive the paypal IPN call and do a handshake by posing it back to paypal
     * @examples https://github.com/paypal/ipn-code-samples/tree/master/php
     * @return null
     */
    public function index() {

        //intialise the ipn handshake and get back the actual data from paypal
        if ($this->initialiseIPN()) {

            //[ontime payment]
            if (request('payment_status') == 'Completed') {
                $this->recordOnetimePayment();
                exit();
            }

            //nothing important
            Log::info("IPN call is not an expected type - will ignore it", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);
        }

        //nothing more
        exit();
    }

    /**
     * - receive initial post data from paypal
     * - post back the data to paypa (handshake)
     * - receive full ipn data from paypal
     * @return string response data from paypal
     */
    public function initialiseIPN() {

        //add the verification string into the post received from paypal
        request()->merge(['cmd' => '_notify-validate']);
        $payload = request()->all();

        //logs
        Log::info("ipn call received. Have added verification string. Will now post back to paypal", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);

        //send back to paypal
        $response = Http::asForm()->post($this->ipn_url, $payload);

        //validate if the handshake request was successful
        if ($response->failed()) {
            Log::info("Handshake Unsuccesful - The server was unable to make an http post (Guzzel) back to paypal", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            return false;
        }

        //check if response back from paypal is the word 'verified'
        if ($response->body() == 'VERIFIED') {
            Log::info("Handshake Success & Verified", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);
            //success
            return true;
        } else {
            Log::info("Handshake Unsuccesful - Paypal was unable to velidate the handshake", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $response->body()]);
            return false;
        }

    }

    /**
     * Save this webhook for processing later by cronjob
     * @return null
     */
    private function recordOnetimePayment() {

        //log
        Log::info("ipn is a valid type for [onetime payment] 'payment_status:completed'. Saving to database", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);

        //avoid duplicates for the same transaction
        if (\App\Models\Webhook::Where('webhooks_payment_transactionid', request('txn_id'))->exists()) {
            Log::info("A transcation for this ipn has already exists in the database. Will skip.", ['process' => '[paypal-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => request()->all()]);
            return;
        }

        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'paypal';
        $webhook->webhooks_type = 'payment_status:completed';
        $webhook->webhooks_payment_type = 'onetime';
        $webhook->webhooks_payment_amount = request('mc_gross');
        $webhook->webhooks_payment_transactionid = request('txn_id');
        $webhook->webhooks_matching_reference = request('item_number');
        $webhook->webhooks_payload = json_encode($_POST);
        $webhook->webhooks_status = 'new';
        $webhook->save();
    }

}