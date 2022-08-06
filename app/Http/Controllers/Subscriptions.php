<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\SubscriptionValidation;
use App\Http\Responses\Subscriptions\CancelResponse;
use App\Http\Responses\Subscriptions\CreateResponse;
use App\Http\Responses\Subscriptions\DestroyResponse;
use App\Http\Responses\Subscriptions\IndexResponse;
use App\Http\Responses\Subscriptions\InvoicesResponse;
use App\Http\Responses\Subscriptions\ShowResponse;
use App\Http\Responses\Subscriptions\StoreResponse;
use App\Http\Responses\Subscriptions\StripePaymentResponse;
use App\Repositories\CategoryRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\StripePaymentRepository;
use App\Repositories\StripeRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

// use Illuminate\Validation\Rule;
// use Validator;

class Subscriptions extends Controller {

    /**
     * The subscription repository instance.
     */
    protected $subscriptionrepo;

    /**
     * The stripe payment repository instance.
     */
    protected $stripepaymentrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The stripe repository instance.
     */
    protected $striperepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    public function __construct(
        StripeRepository $striperepo,
        StripePaymentRepository $stripepaymentrepo,
        UserRepository $userrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        SubscriptionRepository $subscriptionrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //Permissions on methods
        $this->middleware('subscriptionsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'cancelSubscription',
        ]);

        $this->middleware('subscriptionsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('subscriptionsMiddlewareShow')->only([
            'show',
            'setupStripePayment',
        ]);

        $this->middleware('subscriptionsMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->middleware('subscriptionsMiddlewareCancel')->only([
            'cancelSubscription',
        ]);

        $this->subscriptionrepo = $subscriptionrepo;
        $this->striperepo = $striperepo;
        $this->stripepaymentrepo = $stripepaymentrepo;
        $this->userrepo = $userrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;

    }

    /**
     * Display a listing of subscriptions
     * @url baseusr/subscriptions?page=1&source=ext&action=load
     * @urlquery
     *    - [page] numeric|null (pagination page number)
     *    - [source] ext|null  (ext: when called from embedded pages)
     *    - [action] load | null (load: when making additional ajax calls)
     * @return blade view | ajax view
     */
    public function index() {

        //get team members
        $subscriptions = $this->subscriptionrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('subscriptions'),
            'subscriptions' => $subscriptions,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //default
        $show = 'form';
        $message = '';

        //check stripe status
        $api_attempt = $this->striperepo->validateStripe();
        if ($api_attempt !== true) {
            abort(403, $api_attempt);
        }

        //get all stripe products
        if (!$products = $this->striperepo->getProducts()) {
            abort(403, __('lang.error_check_logs_for_details'));
        }

        //check if we have valid products
        $count = 0;
        foreach ($products as $product) {
            //exclude the crm invoice product
            if ($product['id'] != 'dashboard_invoice_default_do_not_delete') {
                $count++;
            }
        }

        //last validation
        if ($count == 0) {
            $show = 'no-products';
        }

        //get all categories (type: project) - for filter panel
        $categories = $categoryrepo->get('project');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'show' => $show,
            'message' => $message,
            'products' => $products->data,
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * get prices linked to a product
     * @return \Illuminate\Http\Response
     */
    public function getProductPrices() {

        //default
        $list = [];

        //get prices for specified product
        if ($prices = $this->striperepo->getProductsPrices(request('product_id'))) {
            foreach ($prices as $price) {

                $rate = subscriptionFormatMoney(($price->unit_amount / 100), $price->currency);
                $interval = subscriptionFormatRenewalInterval($price->recurring->interval_count, $price->recurring->interval);

                $list[] = [
                    'value' => $rate . '/' . $interval,
                    'id' => $price->id,
                ];
            }
        }
        //return feed
        return response()->json($list);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(SubscriptionValidation $request) {

        //all post as array
        $data = request()->all();

        //other settings
        $data['email_client'] = true;

        //create the subscription
        if (!$subscription_id = $this->subscriptionrepo->create($data)) {
            abort(409, sessionErrorMessage());
        }

        //log
        $log = new \App\Models\Log();
        $log->log_creatorid = auth()->id();
        $log->log_text = 'subscription_log_created';
        $log->log_text_type = 'lang';
        $log->log_payload = '';
        $log->logresource_type = 'subscription';
        $log->logresource_id = $subscription_id;
        $log->save();

        //count
        $subscriptions = $this->subscriptionrepo->search();
        $count = $subscriptions->count();

        //get the subscription object (friendly for rendering in blade template)
        $subscriptions = $this->subscriptionrepo->search($subscription_id);
        $subscription = $subscriptions->first();

        /** ----------------------------------------------
         * record event [comment]
         * see database table to details of each key
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'subscription',
            'event_item_id' => $subscription->subscription_id,
            'event_item_lang' => 'event_created_subscription',
            'event_item_content' => __('lang.subscription') . ' - ' . runtimeSubscriptionIdFormat($subscription->subscription_id),
            'event_item_content2' => '',
            'event_parent_type' => 'subscription',
            'event_parent_id' => $subscription->subscription_id,
            'event_parent_title' => $subscription->subscription_gateway_product_name,
            'event_clientid' => $subscription->subscription_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => (is_numeric($subscription->subscription_projectid)) ? 'project' : 'subscription',
            'eventresource_id' => (is_numeric($subscription->subscription_projectid)) ? $subscription->subscription_projectid : $subscription->subscription_id,
            'event_notification_category' => 'notifications_billing_activity',

        ];
        //record event
        $event_id = $this->eventrepo->create($data);

        //event for team
        $users = $this->userrepo->mailingListSubscriptions('email');
        $this->trackingrepo->recordEvent($data, $users, $event_id);

        //send email to client
        if (request('send_email_to_customer') == 'on') {
            if ($user = $this->userrepo->getClientAccountOwner($subscription->subscription_clientid)) {
                $mail = new \App\Mail\SubscriptionCreated($user, [], $subscription);
                $mail->build();
                //event for client
                $this->trackingrepo->recordEvent($data, $user, $event_id);
            }
        }

        //reponse payload
        $payload = [
            'subscriptions' => $subscriptions,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);
    }

    /**
     * Display the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceRepository $invoicerepo, $id) {

        //get the subscription
        $subscriptions = $this->subscriptionrepo->search($id, ['apply_filters' => false]);
        $subscription = $subscriptions->first();

        //page settings
        $page = $this->pageSettings('subscription', $subscription);

        //subscription period
        $interval = subscriptionFormatRenewalInterval($subscription->subscription_gateway_interval, $subscription->subscription_gateway_period);

        //get payments
        $invoices = $invoicerepo->search('', ['bill_subscriptionid' => $id]);

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'subscription')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //reponse payload
        $payload = [
            'page' => $page,
            'subscription' => $subscription,
            'subscription_id' => $id,
            'invoices' => $invoices,
            'interval' => $interval,
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Load more subscription invoices
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function setupStripePayment($id) {

        //get the subscription
        $subscription = \App\Models\Subscription::Where('subscription_id', $id)->first();

        //payment payload
        $data = [
            'subscription_id' => $id,
            'price_id' => $subscription->subscription_gateway_price,
            'cancel_url' => url('subscriptions/' . $subscription->subscription_id),
        ];

        //create a new stripe session
        $session_id = $this->stripepaymentrepo->subscriptionPayment($data);

        //subscription period
        $interval = subscriptionFormatRenewalInterval($subscription->subscription_gateway_interval, $subscription->subscription_gateway_period);

        $payload = [
            'session_id' => $session_id,
            'subscription' => $subscription,
            'interval' => $interval,
        ];

        //response
        return new StripePaymentResponse($payload);

    }

    /**
     * Load more subscription invoices
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function subscriptionInvoices(InvoiceRepository $invoicerepo, $id) {

        //get payments
        $invoices = $invoicerepo->search('', ['bill_subscriptionid' => $id]);

        //reponse payload
        $payload = [
            'invoices' => $invoices,
            'subscription_id' => $id,
        ];

        //response
        return new InvoicesResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the subscription
        $subscription = $this->subscriptionrepo->search($id);

        //not found
        if (!$subscription = $subscription->first()) {
            abort(409, 'The requested subscription could not be loaded');
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'subscription' => $subscription,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [
            'subscription_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'subscription_title' => 'required',
            'subscription_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->subscriptionrepo->update($id)) {
            abort(409);
        }

        //get subscription
        $subscriptions = $this->subscriptionrepo->search($id);

        //reponse payload
        $payload = [
            'subscriptions' => $subscriptions,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified project from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo, $id) {

        //get the subscription
        $subscriptions = $this->subscriptionrepo->search($id, ['apply_filters' => false]);
        $subscription = $subscriptions->first();

        //create a web hook
        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'stripe';
        $webhook->webhooks_type = 'crm-subscription-cancellation';
        $webhook->webhooks_matching_reference = $subscription->subscription_gateway_id;
        $webhook->webhooks_matching_attribute = 'crm-subscription-cancellation';
        $webhook->webhooks_payload = json_encode($subscription);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        $destroyrepo->destroySubscription($id);

        //reponse payload
        $payload = [
            'subscription_id' => $id,
        ];

        //generate a response
        return new DestroyResponse($payload);

    }

    /**
     * End a subscriotion. Queue it for cronjob to cancel also at Stripe
     * @return \Illuminate\Http\Response
     */
    public function cancelSubscription($id) {

        //get the subscription
        $subscriptions = $this->subscriptionrepo->search($id, ['apply_filters' => false]);
        $subscription = $subscriptions->first();

        //create a web hook
        $webhook = new \App\Models\Webhook();
        $webhook->webhooks_gateway_name = 'stripe';
        $webhook->webhooks_type = 'crm-subscription-cancellation';
        $webhook->webhooks_matching_reference = $subscription->subscription_gateway_id;
        $webhook->webhooks_matching_attribute = 'crm-subscription-cancellation';
        $webhook->webhooks_payload = json_encode($subscription);
        $webhook->webhooks_status = 'new';
        $webhook->save();

        //mark subscription as cancelled
        \App\Models\Subscription::where('subscription_id', $id)
            ->update(['subscription_status' => 'cancelled']);

        //get refreshed
        $subscriptions = $this->subscriptionrepo->search($id, ['apply_filters' => false]);

        $payload = [
            'subscription_id' => $id,
            'subscriptions' => $subscriptions,
            'page' => $this->pageSettings('subscriptions'),
        ];

        //generate a response
        return new CancelResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.subscriptions'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'subscriptions',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_subscriptions' => 'active',
            'mainmenu_client_billing' => 'active',
            'submenu_subscriptions' => 'active',
            'sidepanel_id' => 'sidepanel-filter-subscriptions',
            'dynamic_search_url' => url('subscriptions/search?action=search&subscriptionresource_id=' . request('subscriptionresource_id') . '&subscriptionresource_type=' . request('subscriptionresource_type')),
            'add_button_classes' => 'add-edit-subscription-button',
            'load_more_button_route' => 'subscriptions',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => 'Add New Subscription',
            'add_modal_create_url' => url('subscriptions/create?subscriptionresource_id=' . request('subscriptionresource_id') . '&subscriptionresource_type=' . request('subscriptionresource_type')),
            'add_modal_action_url' => url('subscriptions?subscriptionresource_id=' . request('subscriptionresource_id') . '&subscriptionresource_type=' . request('subscriptionresource_type')),
            'add_modal_action_ajax_class' => 'js-ajax-ux-request',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //subscriptions list page
        if ($section == 'subscriptions') {
            $page += [
                'meta_title' => 'Subscriptions',
                'heading' => 'Subscriptions',
                'sidepanel_id' => 'sidepanel-filter-subscriptions',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //subscription page
        if ($section == 'subscription') {
            //adjust
            $page['page'] = 'subscription';
            //add
            $page += [
                'crumbs' => [
                    __('lang.subscription'),
                ],
                'crumbs_special_class' => 'main-pages-crumbs',
                'meta_title' => __('lang.subscription') . ' #' . runtimeSubscriptionIdFormat($data->subscription_id),
                'source_for_filter_panels' => 'ext',
                'section' => 'overview',
            ];

            //crumbs
            $page['crumbs'] = [
                __('lang.subscriptions'),
                runtimeSubscriptionIdFormat($data->subscription_id),
            ];

            //ajax loading and tabs
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //return
        return $page;
    }
}