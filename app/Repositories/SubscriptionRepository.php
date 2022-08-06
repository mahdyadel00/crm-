<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Subscription;
use App\Repositories\StripeRepository;
use App\Repositories\UserRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class SubscriptionRepository {

    /**
     * The repository instance.
     */
    protected $subscription;

    /**
     * The repository instance.
     */
    protected $striperepo;

    /**
     * The repository instance.
     */
    protected $userrepo;

    /**
     * Inject dependecies
     */
    public function __construct(
        StripeRepository $striperepo,
        UserRepository $userrepo,
        Subscription $subscription) {

        $this->subscription = $subscription;
        $this->striperepo = $striperepo;
        $this->userrepo = $userrepo;

    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object subscription collection
     */
    public function search($id = '', $data = []) {

        $subscriptions = $this->subscription->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //joins
        $subscriptions->leftJoin('clients', 'clients.client_id', '=', 'subscriptions.subscription_clientid');
        $subscriptions->leftJoin('users', 'users.id', '=', 'subscriptions.subscription_creatorid');
        $subscriptions->leftJoin('categories', 'categories.category_id', '=', 'subscriptions.subscription_categoryid');
        $subscriptions->leftJoin('projects', 'projects.project_id', '=', 'subscriptions.subscription_projectid');

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $subscriptions->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'subscriptions.subscription_id')
                    ->where('reminders.reminderresource_type', '=', 'subscription')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        // all client fields
        $subscriptions->selectRaw('*');

        //sum payments
        $subscriptions->selectRaw('(SELECT COALESCE(SUM(payment_amount), 0)
                                      FROM payments WHERE payment_subscriptionid = subscriptions.subscription_id
                                      GROUP BY payment_subscriptionid)
                                      AS x_sum_payments');
        $subscriptions->selectRaw('(SELECT COALESCE(x_sum_payments, 0.00))
                AS sum_payments');

        //default where
        $subscriptions->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_subscription_id')) {
            $subscriptions->where('subscription_id', request('filter_subscription_id'));
        }
        if (is_numeric($id)) {
            $subscriptions->where('subscription_id', $id);
        }

        //do not show items that not yet ready (i.e exclude items in the process of being cloned that have status 'invisible')
        $subscriptions->where('subscription_visibility', 'visible');

        //apply filters
        if ($data['apply_filters']) {

            //filter clients
            if (request()->filled('filter_subscription_clientid')) {
                $subscriptions->where('subscription_clientid', request('filter_subscription_clientid'));
            }

            //filter clients
            if (request()->filled('filter_subscription_projectid')) {
                $subscriptions->where('subscription_projectid', request('filter_subscription_projectid'));
            }

            //filter: value (min)
            if (request()->filled('filter_subscription_final_amount_min')) {
                $subscriptions->where('subscription_final_amount', '>=', request('filter_subscription_final_amount_min'));
            }

            //filter: value (max)
            if (request()->filled('filter_subscription_final_amount_max')) {
                $subscriptions->where('subscription_final_amount', '<=', request('filter_subscription_final_amount_max'));
            }

            //filter: subscription date started (start)
            if (request()->filled('filter_subscription_date_started_start')) {
                $subscriptions->whereDate('subscription_date_started', '>=', request('filter_subscription_date_started_start'));
            }

            //filter: subscription date started (end)
            if (request()->filled('filter_subscription_date_started_end')) {
                $subscriptions->whereDate('subscription_date_started', '<=', request('filter_subscription_date_started_end'));
            }

            //filter: subscription date ended (start)
            if (request()->filled('filter_subscription_date_ended_start')) {
                $subscriptions->whereDate('subscription_date_ended', '>=', request('filter_subscription_date_ended_start'));
            }

            //filter: subscription date ended (end)
            if (request()->filled('filter_subscription_date_ended_end')) {
                $subscriptions->whereDate('subscription_date_ended', '<=', request('filter_subscription_date_ended_end'));
            }

            //filter: subscription date renewed (start)
            if (request()->filled('filter_subscription_date_renewed_start')) {
                $subscriptions->whereDate('subscription_date_renewed', '>=', request('filter_subscription_date_renewed_start'));
            }

            //filter: subscription date renewed (end)
            if (request()->filled('filter_subscription_date_renewed_end')) {
                $subscriptions->whereDate('subscription_date_renewed', '<=', request('filter_subscription_date_renewed_end'));
            }

            //stats: - count
            if (isset($data['stats']) && (in_array($data['stats'], [
                'count-pending',
                'count-active',
                'count-failed',
                'count-cancelled',
            ]))) {
                $subscriptions->where('subscription_status', str_replace('count-', '', $data['stats']));
            }

            //filter category
            if (is_array(request('filter_subscription_categoryid')) && !empty(array_filter(request('filter_subscription_categoryid')))) {
                $subscriptions->whereIn('subscription_categoryid', request('filter_subscription_categoryid'));
            }

            //filter status
            if (is_array(request('filter_subscription_status')) && !empty(array_filter(request('filter_subscription_status')))) {
                $subscriptions->whereIn('subscription_status', request('filter_subscription_status'));
            }

            //filter created by
            if (is_array(request('filter_subscription_creatorid')) && !empty(array_filter(request('filter_subscription_creatorid')))) {
                $subscriptions->whereIn('subscription_creatorid', request('filter_subscription_creatorid'));
            }

            //search: various client columns and relationships (where first, then wherehas)
            if (request()->filled('search_query') || request()->filled('query')) {
                $subscriptions->where(function ($query) {
                    //clean for subscription id search
                    $subscription_id = str_replace(config('system.settings_subscriptions_prefix'), '', request('search_query'));
                    $subscription_id = preg_replace("/[^0-9.,]/", '', $subscription_id);
                    $subscription_id = ltrim($subscription_id, '0');
                    $query->Where('subscription_id', '=', $subscription_id);
                    $query->orWhere('subscription_gateway_id', '=', request('search_query'));
                    $query->orWhere('subscription_date_renewed', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    $query->orWhere('subscription_date_next_renewal', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    if (is_numeric(request('search_query'))) {
                        $query->orWhere('subscription_final_amount', '=', request('search_query'));
                    }
                    $query->orWhere('subscription_status', '=', request('search_query'));
                    $query->orWhere('subscription_gateway_product_name', 'LIKE', '%' . request('search_query') . '%');
                    $query->orWhereHas('category', function ($q) {
                        $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                    });
                    $query->orWhereHas('client', function ($q) {
                        $q->where('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                    });
                });
            }
        }

        //filter: value (min)
        if (request()->filled('filter_subscription_payments_min')) {
            $subscriptions->having('sum_payments', '>=', request('filter_subscription_payments_min'));
        }

        //filter: value (max)
        if (request()->filled('filter_subscription_payments_max')) {
            $subscriptions->having('sum_payments', '<=', request('filter_subscription_payments_max'));
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('subscriptions', request('orderby'))) {
                $subscriptions->orderBy(request('orderby'), request('sortorder'));
            }
            //others client
            switch (request('orderby')) {
            case 'client':
                $subscriptions->orderBy('client_company_name', request('sortorder'));
                break;
            case 'subscription_id':
                $subscriptions->orderBy('subscription_id', request('sortorder'));
                break;
            case 'plan':
                $subscriptions->orderBy('subscription_gateway_product_name', request('sortorder'));
                break;
            case 'amount':
                $subscriptions->orderBy('subscription_final_amount', request('sortorder'));
                break;
            case 'date_renewed':
                $subscriptions->orderBy('subscription_date_renewed', request('sortorder'));
                break;
            case 'date_next':
                $subscriptions->orderBy('subscription_date_next_renewal', request('sortorder'));
                break;
            case 'payments':
                $subscriptions->orderBy('sum_payments', request('sortorder'));
                break;
            case 'status':
                $subscriptions->orderBy('subscription_status', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $subscriptions->orderBy(
                'subscription_id',
                'DESC'
            );
        }

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'count-new',
            'count-active',
            'count-cancelled',
            'count-failed',
        ]))) {
            return $subscriptions->count();
        }

        // Get the results and return them.
        if (isset($data['limit']) && is_numeric($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = config('system.settings_system_pagination_limits');
        }

        return $subscriptions->paginate($limit);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create($data = []) {

        //new object
        $subscription = new $this->subscription;

        //reset error messages
        session(['error_message' => __('lang.action_not_completed_errors_found')]);

        //started
        Log::info("subscription creation process has started", ['process' => '[create-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);

        //required data
        $required = [
            'subscription_categoryid',
            'subscription_gateway_product',
            'subscription_gateway_price',
            'subscription_clientid',
        ];

        //basic validation
        foreach ($required as $key => $value) {
            if (!isset($data[$value])) {
                Log::error("creating subscription failed - required data is missing", ['process' => '[create-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
                session(['error_message' => __('lang.required_data_is_missing')]);
                return false;
            }
        }

        //get the stripe product
        if (!$product = $this->striperepo->getProduct($data['subscription_gateway_product'])) {
            Log::error("creating subscription failed - stripe product could not be retrieved from stripe", ['process' => '[create-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'product_id' => $data['subscription_gateway_product']]);
            session(['error_message' => __('lang.required_data_is_missing')]);
            return false;
        }

        //get the stripe price
        if (!$price = $this->striperepo->getPrice($data['subscription_gateway_price'])) {
            Log::error("creating subscription failed - stripe price could not be retrieved from stripe", ['process' => '[create-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'price_id' => $data['subscription_gateway_price']]);
            session(['error_message' => __('lang.required_data_is_missing')]);
            return false;
        }

        //validate that the currency matches the system currency
        if (strtolower($price->currency) != strtolower(config('system.settings_system_currency_code'))) {
            Log::error("creating subscription failed - price currency does not match system currency", ['process' => '[create-subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            session(['error_message' => __('lang.subscription_currency_mismatch')]);
            return false;
        }

        //create subscription
        $subscription->subscription_categoryid = $data['subscription_categoryid'];
        $subscription->subscription_creatorid = auth()->id();
        $subscription->subscription_gateway_product_name = $product->name;
        $subscription->subscription_gateway_product = $data['subscription_gateway_product'];
        $subscription->subscription_gateway_price = $data['subscription_gateway_price'];
        $subscription->subscription_clientid = $data['subscription_clientid'];
        $subscription->subscription_projectid = $data['subscription_projectid'];
        $subscription->subscription_gateway_interval = $price->recurring->interval_count;
        $subscription->subscription_gateway_period = $price->recurring->interval;
        $subscription->subscription_subtotal = $price->unit_amount / 100;
        $subscription->subscription_amount_before_tax = $price->unit_amount / 100;
        $subscription->subscription_tax_percentage = 0;
        $subscription->subscription_tax_amount = 0;
        $subscription->subscription_final_amount = $price->unit_amount / 100;

        //save and return id
        if ($subscription->save()) {
            //return
            return $subscription->subscription_id;
        } else {
            Log::error("unable to create subscription - database error", ['process' => '[ItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Calculate the next renewal data for a subscription. This is used in cronjobs, to calculate next
     * date from [today] (the date payment has been made)
     * @return mixed int|bool
     */
    public function nextRenewalDate($subscription = '') {

        //validate
        if (!$subscription instanceof \App\Models\Subscription) {
            return '';
        }

        //duration
        $interval = $subscription->subscription_gateway_interval;

        //calculate
        switch ($subscription->subscription_gateway_period) {
        case 'day':
            return \Carbon\Carbon::now()->addDays($interval)->format('Y-m-d');
            break;
        case 'week':
            return \Carbon\Carbon::now()->addWeeks($interval)->format('Y-m-d');
            break;
        case 'month':
            return \Carbon\Carbon::now()->addMonths($interval)->format('Y-m-d');
            break;
        case 'year':
            return \Carbon\Carbon::now()->addYears($interval)->format('Y-m-d');
            break;
        }

    }
}