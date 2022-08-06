<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Invoices;
use Closure;
use Log;

class Edit {

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
            Log::error("invoice could not be found", ['process' => '[permissions][invoices][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice id' => $bill_invoiceid ?? '']);
            abort(409, __('lang.invoice_not_found'));
        }

        //permission: does user have permission edit invoices
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_invoices >= 2) {

                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][invoices][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //vivibility
        config(['visibility.invoice_modal_client_project_fields' => false]);

    }
}
