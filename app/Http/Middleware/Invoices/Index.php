<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Invoices;

use App\Models\Invoice;
use Closure;
use Log;
use Route;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] invoices
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        /** -------------------------------------------------------------------------
         * This is a list of web routes that use this middleware but we do not want
         * to run the modues visibilty checks
         * -------------------------------------------------------------------------*/
        $excluded_routes = [
            'payments.store',
        ];

        //validate module status
        if (!config('visibility.modules.invoices')) {
            //ignore if we are doing something with payments (like adding a payment to an invocie)
            if (!in_array(Route::currentRouteName(), $excluded_routes)) {
                abort(404, __('lang.the_requested_service_not_found'));
            }
            return $next($request);
        }

        //various frontend and visibility settings
        $this->fronteEnd();

        //embedded request: limit by supplied resource data
        if (request()->filled('invoiceresource_type') && request()->filled('invoiceresource_id')) {
            //project invoices
            if (request('invoiceresource_type') == 'project') {
                request()->merge([
                    'filter_bill_projectid' => request('invoiceresource_id'),
                ]);
            }
            //client invoices
            if (request('invoiceresource_type') == 'client') {
                request()->merge([
                    'filter_bill_clientid' => request('invoiceresource_id'),
                ]);
            }
        }

        //client user permission
        if (auth()->user()->is_client) {
            if (auth()->user()->is_client_owner) {
                //exclude draft invoices & sanity client
                request()->merge([
                    'filter_invoice_exclude_status' => 'draft',
                    'filter_bill_clientid' => auth()->user()->clientid,
                ]);
                return $next($request);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_invoices >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][invoices][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('foo') with urlResource('foo'), in blade templated
         * */
        if (request('invoiceresource_type') != '' || is_numeric(request('invoiceresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&invoiceresource_type=' . request('invoiceresource_type') . '&invoiceresource_id=' . request('invoiceresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //default show some table columns
        config([
            'visibility.invoices_col_client' => true,
            'visibility.invoices_col_project' => true,
            'visibility.invoices_col_payments' => true,
            'visibility.filter_panel_client_project' => true,
        ]);

        //permissions -viewing
        if (auth()->user()->role->role_invoices >= 1) {
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
                    'visibility.invoices_col_client' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_invoices >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.invoices_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_invoices >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('invoiceresource_type') == 'project') {
            config([
                //visibility
                'visibility.invoices_col_client' => false,
                'visibility.invoices_col_project' => false,
                'visibility.filter_panel_client_project' => false,
            ]);
        }

        //columns visibility
        if (request('invoiceresource_type') == 'client') {
            config([
                //visibility
                'visibility.invoices_col_client' => false,
                'visibility.invoices_col_payments' => false,
                'visibility.filter_panel_client_project' => false,
                'visibility.filter_panel_clients_projects' => true,
            ]);
        }
    }
}
