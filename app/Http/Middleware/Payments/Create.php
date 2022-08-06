<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Payments;
use Closure;
use Log;

class Create {

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

        //frontend
        $this->fronteEnd();

        //if invoice id has been passed, validate
        if (request()->filled('bill_invoiceid')) {
            if (!\App\Models\Invoice::Where('bill_invoiceid', request('bill_invoiceid'))->first()) {
                abort(409, __('lang.item_not_found'));
            }
            request()->merge([
                'payment_invoiceid' => request('bill_invoiceid'),
            ]);
        }

        //permission: does user have permission create payments
        if (auth()->user()->role->role_invoices >= 2) {

            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][payments][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default settings
        config([
            'settings.visibiliy_payment_modal_invoice_field' => true,
        ]);

        //hide invoice field
        if (request()->filled('bill_invoiceid')) {
            config([
                'settings.visibiliy_payment_modal_invoice_field' => false,
                'settings.visibiliy_payment_modal_meta' => true,
            ]);
        }

        //clicked from topnav 'add' button
        if (request('ref') == 'quickadd') {
            config([
                'visibility.payment_show_payment_option' => true,
            ]);
        }

    }
}
