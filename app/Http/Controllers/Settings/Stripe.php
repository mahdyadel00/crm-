<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for stripe settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Stripe\IndexResponse;
use App\Http\Responses\Settings\Stripe\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Validator;

class Stripe extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //settings
        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_stripe_secret_key' => 'required',
            'settings_stripe_public_key' => 'required',
            'settings_stripe_webhooks_key' => 'required',
            'settings_stripe_currency' => 'required',
            'settings_stripe_display_name' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //test api connection (validate the key)
        try {
            //set key
            \Stripe\Stripe::setApiKey(request('settings_stripe_secret_key'));
            //try a basic request
            $endpoints = \Stripe\WebhookEndpoint::all(['limit' => 1]);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            abort(409, __('lang.stripe_authentication_error'));
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            abort(409, __('lang.stripe_network_error'));
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            abort(409, __('lang.stripe_generic_error').' - '. $error_message);
        }

        //update settings
        if (!$this->settingsrepo->updateStripeSettings()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.payment_methods'),
                'stripe',
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];
        return $page;
    }

}
