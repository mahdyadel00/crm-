<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Payments;

use App\Models\Payment;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] payments
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.payments')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //various frontend and visibility settings
        $this->fronteEnd();

        //set for dynamically loading the payment modal
        $this->dynamicLoad();

        //embedded request: limit by supplied resource data
        if (request()->filled('paymentresource_type') && request()->filled('paymentresource_id')) {
            //project payments
            if (request('paymentresource_type') == 'project') {
                request()->merge([
                    'filter_payment_projectid' => request('paymentresource_id'),
                ]);
            }
            //client payments
            if (request('paymentresource_type') == 'client') {
                request()->merge([
                    'filter_payment_clientid' => request('paymentresource_id'),
                ]);
            }
            //invoice payments
            if (request('paymentresource_type') == 'invoice') {
                request()->merge([
                    'filter_payment_invoiceid' => request('paymentresource_id'),
                ]);
            }

        }

        //client user permission
        if (auth()->user()->is_client) {
            if (auth()->user()->is_client_owner) {
                //sanity client
                request()->merge([
                    'filter_payment_clientid' => auth()->user()->clientid,
                ]);
                return $next($request);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_payments >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][payments][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('payment') with urlResource('payment'), in blade templated
         * */
        if (request('paymentresource_type') != '' || is_numeric(request('paymentresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&paymentresource_type=' . request('paymentresource_type') . '&paymentresource_id=' . request('paymentresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.payments_col_client' => true,
            'visibility.payments_col_project' => true,
            'visibility.filter_panel_client_project' => true,
            'visibility.payments_col_method' => true,
            'visibility.payments_col_id' => true,
            'visibility.payments_col_action' => true,
            'visibility.payments_col_invoiceid' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_payments >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => true,
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.payments_col_client' => false,
                    'visibility.payments_col_method' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_payments >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.payments_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_payments >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('paymentresource_type') == 'project') {
            config([
                //visibility
                'visibility.payments_col_client' => false,
                'visibility.payments_col_project' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }

        //columns visibility
        if (request('paymentresource_type') == 'client') {
            config([
                //visibility
                'visibility.payments_col_client' => false,
                'visibility.filter_panel_client_project' => false,
                'visibility.payments_col_method' => false,
                'visibility.filter_panel_clients_projects' => true,
            ]);
        }

        //column visibility
        if (request('paymentresource_type') == 'invoice') {
            config([
                //visibility
                'visibility.payments_col_id' => false,
                'visibility.payments_col_client' => false,
                'visibility.filter_panel_client_project' => false,
                'visibility.payments_col_action' => false,
                'visibility.payments_col_invoiceid' => false,
                'visibility.payments_col_checkboxes' => false,
                'visibility.payments_col_project' => false,
            ]);
        }

    }

    /*
     * set the front end to load the modal dynamically
     */
    private function dynamicLoad() {
        //validate that the url is for loading a payment dynmically
        if (is_numeric(request()->route('payment')) && request()->segment(2) == 'v') {
            config([
                'visibility.dynamic_load_modal' => true,
                'settings.dynamic_trigger_dom' => '#dynamic-payment-content',
            ]);
        }
    }
}
