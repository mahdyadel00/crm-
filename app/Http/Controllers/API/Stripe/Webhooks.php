<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for webhooks from stripe
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\API\Stripe;
use App\Http\Controllers\Controller;
use Log;

class Webhooks extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        $this->middleware('guest');

    }

    /**
     * Receive and process stripe webhook
     * @return null
     */
    public function index() {

        //get the payload body
        $payload = @file_get_contents('php://input');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $_SERVER['HTTP_STRIPE_SIGNATURE'], config('system.settings_stripe_webhooks_key')
            );
        } catch (\UnexpectedValueException $e) {
            Log::error("stripe webhook data is invalid", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            http_response_code(400);
            die('Stripe payload is invalid');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::critical("Stripe signing id (signature) does not match the one in database", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $payload]);
            http_response_code(400);
            die('Signing signature does not match');
        }

        //checkout session complete
        if ($event->type == 'checkout.session.completed') {
            //session object
            $session = $event->data->object;
            $this->checkoutSessionCompleted($session);
        }

        //save to database - subscription renewed
        if ($event->type == 'invoice.payment_succeeded') {
            //session object
            $session = $event->data->object;
            $this->subscriptionRenewed($session);
        }

        //save to database - subscription cancelled
        if ($event->type == 'customer.subscription.deleted') {
            //session object
            $session = $event->data->object;
            $this->subscriptionCancelled($session);
        }
    }

    /**
     * Save this webhook for processing later by cronjob
     * @param object $session stripe session object
     * @return null
     */
    private function checkoutSessionCompleted($session) {

        //log
        Log::info("webhook is for a completed new payment 'checkout.session.completed'. Saving databasse", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);

        //avoid duplicates for the same transaction
        if (\App\Models\Webhook::Where('webhooks_matching_reference', $session->id)->exists()) {
            Log::info("A transcation for this webhook has already exists in the database. Will skip.", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);
            return;
        }

        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'stripe';
        $webhook->webhooks_type = 'checkout.session.completed';
        $webhook->webhooks_payment_type = ($session->mode == 'subscription') ? 'subscription' : 'onetime';
        $webhook->webhooks_payment_amount = null;
        $webhook->webhooks_payment_transactionid = $session->payment_intent;
        $webhook->webhooks_matching_reference = $session->id;
        $webhook->webhooks_payload = json_encode($session);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        //inform stripe "all ok"
        http_response_code(200);
        exit('Webhook Received Ok');
    }

    /**
     * Save this webhook for processing later by cronjob
     * @param object $session stripe session object
     * @return null
     */
    private function subscriptionRenewed($session) {

        //log
        Log::info("webhook is a valid type for subscription renewal - 'invoice.payment_succeeded'. Saving to database", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);

        //avoid duplicates for the same transaction
        if (\App\Models\Webhook::Where('webhooks_payment_transactionid', $session->charge)->exists()) {
            Log::info("A record for this webhook has already exists in the database. Will skip.", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);
            return;
        }

        //make sure this is not for the first payment
        if ($session->billing_reason == 'subscription_create') {
            Log::info("We have already recorded subscription ($session->subscription) payment using (checkout.session.completed) - Will create a webhook for updating the transaction ID ($session->charge) and exit", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);
            $webhook = new \App\Models\Webhook();
            $webhook->webhooks_gateway_name = 'stripe';
            $webhook->webhooks_type = 'crm-subscription-transation-id';
            $webhook->webhooks_payment_type = 'subscription';
            $webhook->webhooks_payment_amount = $session->total / 100;
            $webhook->webhooks_payment_transactionid = $session->payment_intent;
            $webhook->webhooks_matching_reference = $session->subscription; //subscription id
            $webhook->webhooks_matching_attribute = 'subscription-transaction-id';
            $webhook->webhooks_payload = json_encode($session);
            $webhook->webhooks_status = 'new';
            $webhook->save();
            return;
        }

        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'stripe';
        $webhook->webhooks_type = 'invoice.payment_succeeded';
        $webhook->webhooks_payment_type = 'subscription';
        $webhook->webhooks_payment_amount = $session->total / 100;
        $webhook->webhooks_payment_transactionid = $session->payment_intent;
        $webhook->webhooks_matching_reference = $session->subscription; //subscription id
        $webhook->webhooks_matching_attribute = 'subscription-renewed';
        $webhook->webhooks_payload = json_encode($session);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        //inform stripe "all ok"
        http_response_code(200);
        exit('Webhook Received Ok');
    }

    /**
     * Save this webhook for processing later by cronjob
     * @param object $session stripe session object
     * @return null
     */
    private function subscriptionCancelled($session) {

        //log
        Log::info("webhook is a valid type for subscription cancelled - 'customer.subscription.deleted'. Saving to database", ['process' => '[stripe-webhooks]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session' => $session]);

        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'stripe';
        $webhook->webhooks_type = 'customer.subscription.deleted';
        $webhook->webhooks_matching_reference = $session->id;
        $webhook->webhooks_matching_attribute = 'subscription-cancelled';
        $webhook->webhooks_payload = json_encode($session);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        //inform stripe "all ok"
        http_response_code(200);
        exit('Webhook Received Ok');
    }
}