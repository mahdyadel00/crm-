<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for payment gateways
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Log;

class StripeRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * check if Stripe gateway is configured correctly
     * @return mixed error message or true
     */
    public function validateStripe() {

        //check if we have settings for stripe in the database
        if (config('system.settings_stripe_secret_key') == '' || config('system.settings_stripe_public_key') == '' || config('system.settings_stripe_currency') == '') {
            return __('lang.stripe_authentication_error');
        }

        //check if we have settings for stripe in the database
        if (config('system.settings_stripe_status') != 'enabled') {
            return __('lang.stripe_not_enabled');
        }

        //test api connection (validate the key)
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            //make asimple request to check key is valide
            $stripe->webhookEndpoints->all(['limit' => 3]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return __('lang.stripe_authentication_error');
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            return __('lang.stripe_network_error');
        } catch (Exception $e) {
            return __('lang.stripe_generic_error');
        }

        //all is ok
        return true;
    }

    /**
     * get aan array of all the products in Stripe
     * @param string $product_di the unique stripe product id
     * @return mixed error message or true
     */
    public function getProducts() {

        //get all products
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $products = $stripe->products->all();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-products]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-products]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-products]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //final check
        if (!is_object($products)) {
            Log::error("unable to retrieve your products from stripe", ['process' => '[stripe-get-products]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //return array of the products
        return $products;
    }

    /**
     * get an array of all the prices for a product in Stripe
     * @return mixed error message or true
     */
    public function getProductsPrices($product_id = '') {

        //validate
        if ($product_id == '') {
            Log::error('no product id was specifid', ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //get all products
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $prices = $stripe->prices->all(['product' => $product_id]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //final check
        if (!is_object($prices)) {
            Log::error("unable to retrieve your products from stripe", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //return array of the products
        return $prices;
    }

    /**
     * get a specific product
     * @param string $product_di the unique stripe product id
     * @return mixed error message or true
     */
    public function getProduct($product_id = '') {

        //validate
        if ($product_id == '') {
            Log::error('no product id was specifid', ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //get all products
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $product = $stripe->products->retrieve($product_id, []);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //final check
        if (!is_object($product)) {
            Log::error("unable to retrieve your products from stripe", ['process' => '[stripe-get-product]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id'=> $product_id]);
            return false;
        }

        //return array of the products
        return $product;
    }

    /**
     * get a specific price
     * @param string $price_id the unique stripe price id
     * @return mixed error message or true
     */
    public function getPrice($price_id = '') {

        //validate
        if ($price_id == '') {
            Log::error('no stripe price_id was specifid', ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id'=> $price_id]);
            return false;
        }

        //get all products
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $price = $stripe->prices->retrieve($price_id, []);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id'=> $price_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id'=> $price_id]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id'=> $price_id]);
            return false;
        }

        //final check
        if (!is_object($price)) {
            Log::error("unable to retrieve your products from stripe", ['process' => '[stripe-get-products-prices]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id'=> $price_id]);
            return false;
        }

        //return array of the products
        return $price;
    }

    /**
     * get a subscription from stripe
     * @param string $subscription_stripe_id the unique stripe id
     * @return mixed error message or true
     */
    public function getSubscription($subscription_stripe_id = '') {

        //validation
        if ($subscription_stripe_id == '') {
            Log::error("Stripe Error - a subscription id was not provided", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id'=> $subscription_stripe_id]);
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $subscription = $stripe->subscriptions->retrieve(
                $subscription_stripe_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id'=> $subscription_stripe_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id'=> $subscription_stripe_id]);
            return false;
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id'=> $subscription_stripe_id]);
            return false;
        }

        //final check
        if (!is_object($subscription)) {
            Log::error("unable to retrieve the subscription from stripe", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_stripe_id'=> $subscription_stripe_id]);
            return false;
        }

        //return the subscription
        return $subscription;
    }


        /**
     * get a subscription from stripe
     * @param string $subscription_stripe_id the unique stripe id
     * @return mixed error message or true
     */
    public function cancelSubscription($subscription_stripe_id = '') {

        //validation
        if ($subscription_stripe_id == '') {
            Log::error("Stripe Error - a subscription id was not provided", ['process' => '[stripe-cancel-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id'=> $subscription_stripe_id]);
            return false;
        }

        //get the subscription
        try {
            $stripe = new \Stripe\StripeClient(config('system.settings_stripe_secret_key'));
            $stripe->subscriptions->cancel(
                $subscription_stripe_id,
                []
            );
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error("Stripe Error - Unable to authenticate with Stripe. Check your API keys", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id'=> $subscription_stripe_id]);
            return false;
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error("Stripe Network Error - Your server was unable to connect to api.stripe.com", ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id'=> $subscription_stripe_id]);
            return false;
        }catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id'=> $subscription_stripe_id]);
            return false;
        }catch (Exception $e) {
            Log::error($e->getMessage(), ['process' => '[stripe-get-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'subscription_id'=> $subscription_stripe_id]);
            return false;
        }

        //return the subscription
        return true;
    }
}