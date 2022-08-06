<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Payments;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the payment exists
     *   2. checks users permissions to [view] the payment
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

        //payment id
        $payment_id = $request->route('payment');

        //does the payment exist
        if ($payment_id == '' || !$payment = \App\Models\Payment::Where('payment_id', $payment_id)->first()) {
            abort(404);
        }

        //team: does user have permission edit payments
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_invoices >= 1) {

                return $next($request);
            }
        }

        //client: does user have permission edit payments
        if (auth()->user()->is_client) {
            if ($payment->payment_clientid == auth()->user()->clientid) {
                //only account owner
                if (auth()->user()->account_owner == 'yes') {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][payments][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payment id' => $payment_id ?? '']);
        abort(403);
    }
}
