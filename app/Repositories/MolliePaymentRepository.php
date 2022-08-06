<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for mollie payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Illuminate\Support\Str;
use Log;
use \Mollie\Api;

class MolliePaymentRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /** ----------------------------------------------------
     * [onetime payment]
     * Start the process for a single mollie payment
     * @param array $data information payload
     * @return int session id
     * ---------------------------------------------------*/
    public function onetimePayment($data = []) {

        Log::info("mollie onetime payment request initiated", ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);


        //validate
        if (!is_array($data)) {
            Log::error("invalid paymment payload data", ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        // Generate random transaction id
        $receipt_id = Str::random(20);

        //connect to mollie
        try {
            $mollie = new Api\MollieApiClient();
            $mollie->setApiKey($this->getKey());
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error("Mollie Error - " . $e->getMessage(), ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error("Mollie Error - " . $e->getMessage(), ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //make a payment
        try {
            $order = $mollie->payments->create([
                "amount" => [
                    "currency" => $data['currency'],
                    "value" => $data['amount'],
                ],
                "description" => __('lang.invoice_payment'),
                "redirectUrl" => $data['thank_you_url'],
                "webhookUrl" => $data['webhooks_url'],
            ]);
        } catch (\Mollie\Api\Exceptions\ApiException $e) {
            Log::error("Mollie Error - " . $e->getMessage(), ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error("Mollie Error - " . $e->getMessage(), ['process' => '[mollie-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save session id in sessions database
        $payment_session = new \App\Models\PaymentSession();
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'mollie';
        $payment_session->session_gateway_ref = $order->id;
        $payment_session->session_amount = $data['amount'];
        $payment_session->session_invoices = $data['invoice_id'];
        $payment_session->save();

        //return the url to redirect to mollie
        return $order->getCheckoutUrl();

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
