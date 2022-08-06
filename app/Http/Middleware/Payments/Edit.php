<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Payments;
use Closure;
use Log;

class Edit {

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

        //payment id
        $payment_id = $request->route('payment');

        //frontend
        $this->fronteEnd();

        //does the payment exist
        if ($payment_id == '' || !$payment = \App\Models\Payment::Where('payment_id', $payment_id)->first()) {
            Log::error("payment could not be found", ['process' => '[payments][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payment id' => $payment_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit payments
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_payments >= 2) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][payments][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config(['visibility.some_item' => true]);

        //clients
        if (auth()->user()->is_team) {

        }

        //clients
        if (auth()->user()->is_client) {

        }

    }
}