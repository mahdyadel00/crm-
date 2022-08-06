<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Invoices;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the invoice exists
     *   2. checks users permissions to [view] the invoice
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.invoices')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //invoice id
        $bill_invoiceid = $request->route('invoice');

        //frontend
        $this->fronteEnd();

        //does the invoice exist
        if ($bill_invoiceid == '' || !$invoice = \App\Models\Invoice::Where('bill_invoiceid', $bill_invoiceid)->first()) {
            abort(404);
        }

        //team: does user have permission edit invoices
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_invoices >= 1) {

                return $next($request);
            }
        }

        //client: does user have permission edit invoices
        if (auth()->user()->is_client) {
            //404 for invoices in draft status
            if ($invoice->bill_status == 'draft') {
                abort(404);
            }
            //ok
            if ($invoice->bill_clientid == auth()->user()->clientid) {
                //only account owner
                if (auth()->user()->account_owner == 'yes') {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][invoices][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice id' => $bill_invoiceid ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //merge data
        request()->merge([
            'resource_query' => 'ref=page',
        ]);

        //default view (not editing)
        config([
            'visibility.bill_mode' => 'viewing',
        ]);

        //are we in editing mode
        if (request()->segment(3) == 'edit-invoice') {
            config([
                'visibility.bill_mode' => 'editing',
                'css.bill_mode' => 'editing',
            ]);
        }

        //stripe js
        if (config('system.settings_stripe_status') == 'enabled') {
            config([
                'visibility.stripe_js' => true,
            ]);
        }

        //razorpay js
        if (config('system.settings_razorpay_status') == 'enabled') {
            config([
                'visibility.razorpay_js' => true,
            ]);
        }

        //page level javascript
        config(['js.section' => 'bill']);
    }
}
