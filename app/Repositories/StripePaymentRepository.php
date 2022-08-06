<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for stripe payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use App\Repositories\StripeRepository;
use Exception;
use Log;

class StripePaymentRepository {

    /**
     * The stripe repository instance.
     */
    protected $striperepo;

    /**
     * Inject dependecies
     */
    public function __construct(StripeRepository $striperepo) {

        $this->striperepo = $striperepo;

        //set stripe key
        try {
            \Stripe\Stripe::setApiKey(config('system.settings_stripe_secret_key'));
            \Stripe\Stripe::setApiVersion("2020-03-02");
        } catch (Exception $e) {
            Log::critical("unable to connect to stripe", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }
    }

    /** ----------------------------------------------------
     * [onetime payment]
     * Start the process for a single stripe payment
     * generate a payment session id
     *
     * @return mixed stripe customer object or bool (false)
     * ---------------------------------------------------*/
    public function onetimePayment($data = []) {

        //validate
        if (!is_array($data)) {
            Log::error("invalid paymment payload data", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the customer from stripe
        if (!$customer = $this->getCustomer(auth()->id())) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the default product getDefaultOnetimeProduct
        if (!$product = $this->getDefaultOnetimeProduct()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //create a new payment session
        $data['customer_id'] = $customer->id;
        if (!$session = $this->createOnetimePaymentSession($data)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //save session id in sessions database
        $payment_session = new \App\Models\PaymentSession();
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'stripe';
        $payment_session->session_gateway_ref = $session->id;
        $payment_session->session_amount = $data['amount'];
        $payment_session->session_invoices = $data['invoice_id'];
        $payment_session->save();

        //return the session id
        return $session->id;

    }

    /** ----------------------------------------------------
     * [subscription payment]
     * Start the process for a subscription stripe payment
     * generate a pryment sessions id
     *
     * @return mixed stripe customer object or bool (false)
     * ---------------------------------------------------*/
    public function subscriptionPayment($data = []) {

        //validate
        if (!is_array($data)) {
            Log::error("invalid paymment payload data", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the customer from stripe
        if (!$customer = $this->getCustomer(auth()->id())) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //create a new payment session
        $data['customer_id'] = $customer->id;
        if (!$session = $this->createSubscriptionPaymentSession($data)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //validate the price and get the prices
        if (!$price = $this->striperepo->getPrice($data['price_id'])) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //save session id in sessions database
        $payment_session = new \App\Models\PaymentSession();
        $payment_session->session_creatorid = auth()->id();
        $payment_session->session_creator_fullname = auth()->user()->first_name . ' ' . auth()->user()->last_name;
        $payment_session->session_creator_email = auth()->user()->email;
        $payment_session->session_gateway_name = 'stripe';
        $payment_session->session_gateway_ref = $session->id;
        $payment_session->session_amount = $price->unit_amount / 100;
        $payment_session->session_invoices = null;
        $payment_session->session_subscription = $data['subscription_id'];
        $payment_session->save();

        //return the session id
        return $session->id;

    }

    /** --------------------------------------------------------------------------------------------
     * [get customer]
     * - if this user has a stripe id in our database, attempt to get the user from stripe
     * - else, create a new user in stripe
     * @source https://stripe.com/docs/api/customers/retrieve
     * @source https://stripe.com/docs/api/customers/create
     * @param int user_id
     * @return mixed stripe customer object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function getCustomer($user_id = '') {

        //validate
        if (!is_numeric($user_id)) {
            return false;
        }

        //get logged in user
        if (!$user = \App\Models\User::Where('id', auth()->id())->first()) {
            Log::error("the user could not be found in our dashboard", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => $user_id]);
            return false;
        }

        //check if the current user is a stripe customer.
        if ($user->thridparty_stripe_customer_id != '') {
            //get the customer
            try {
                $customer = \Stripe\Customer::retrieve(auth()->user()->thridparty_stripe_customer_id);
                return $customer;
            } catch (exception $e) {
                Log::info("this user has a stripe customer id, but the user was not found in stripe - will recreate the user", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => $user_id, 'error_message' => $e->getMessage()]);
            }
        }

        //create a new customer in stripe
        try {
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => $user->first_name . ' ' . $user->last_name,
                'metadata' => [
                    'userid' => $user->id,
                    'clientid' => $user->clientid,
                ],
            ]);
            //update customer profile with stripe id
            $user->thridparty_stripe_customer_id = $customer->id;
            $user->save();
            //return
            return $customer;
        } catch (exception $e) {
            Log::error("error retrieving customer from stripe", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id(), 'error_message' => $e->getMessage()]);
        }

        //return
        return false;

    }

    /** --------------------------------------------------------------------------------------------
     * [get default product]
     * - get the default product from inside stripe. This is the base product used for pricing
     * - else, create a new one
     * @source https://stripe.com/docs/payments/checkout
     * @return mixed stripe product object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function getDefaultOnetimeProduct() {

        //create the product
        try {
            $product = \Stripe\Product::retrieve('dashboard_invoice_default_do_not_delete');
            return $product;
        } catch (exception $e) {
            Log::info("default onetime product not found in stripe. will attempt to create one", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
        }

        //create the product
        try {
            $product = \Stripe\Product::create([
                'name' => 'Invoice',
                'id' => 'dashboard_invoice_default_do_not_delete',
            ]);
            return $product;
        } catch (exception $e) {
            Log::critical("creating stripe default onetime product has failed", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
        }

        return false;
    }

    /** --------------------------------------------------------------------------------------------
     * [create onetime session]
     * - create a payment intent session. This session will also be returned by stripe in the
     *   paymeht complete webhook (checkout.session.completed) webhook
     * @source https://stripe.com/docs/payments/checkout
     * @return mixed stripe product object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function createOnetimePaymentSession($data = []) {

        //create the product
        try {
            $session = \Stripe\Checkout\Session::create([
                'customer' => $data['customer_id'],
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'unit_amount' => runtimeAmountInCents($data['amount']),
                        'currency' => $data['currency'],
                        'product' => 'dashboard_invoice_default_do_not_delete',
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => url('payments/thankyou?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => $data['cancel_url'],
            ]);
            return $session;
        } catch (exception $e) {
            Log::info("default onetime product not found in stripe. will attempt to create one", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
        }

        return false;
    }

    /** --------------------------------------------------------------------------------------------
     * [create subsccription session]
     * - create a payment intent session. This session will also be returned by stripe in the
     *   paymeht complete webhook (checkout.session.completed) webhook
     * @source https://stripe.com/docs/payments/checkout
     * @return mixed stripe product object or bool(false)
     * -------------------------------------------------------------------------------------------*/
    public function createSubscriptionPaymentSession($data = []) {

        //create the product
        try {
            $session = \Stripe\Checkout\Session::create([
                'customer' => $data['customer_id'],
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $data['price_id'],
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => url('payments/thankyou?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => $data['cancel_url'],
                'metadata' => [
                     'subscription_id' =>$data['subscription_id'],
                ],
            ]);
            return $session;
        } catch (exception $e) {
            Log::info("default onetime product not found in stripe. will attempt to create one", ['process' => '[StripePaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $e->getMessage()]);
        }

        return false;
    }
}