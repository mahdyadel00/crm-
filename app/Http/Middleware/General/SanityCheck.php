<?php
/** ----------------------------------------------------------------------------------------
 *  [NEXTLOOP][SANITY CHECK SERVICE PROVIDER]
 *
 *  - Validate arrays that are in the settings file
 *  -Checks if setup has been completed. If not, user is redirected to setup
 *  - If setup has been completed and user is trying to access the setup page
 *    they will be redirected to home page
 *
 * -----------------------------------------------------------------------------------------*/
namespace App\Http\Middleware\General;
use Closure;
use File;
use Log;

class SanityCheck {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //CHECK IF SETUP COMPLETED - REDIRECT TO SETUP PAGE
        if (env('SETUP_STATUS') != 'COMPLETED') {
            if (request()->route()->getName() != 'setup') {
                //redirect to setup page
                return redirect()->route('setup');
            }
        }
        
        //TRYING TO ACCESS SETUP WHEN ITS ALREADY BEEN COMPLETED
        if (env('SETUP_STATUS') == 'COMPLETED') {
            if (request()->route()->getName() == 'setup') {
                //redirect to setup page
                return redirect()->route('home');
            }
        }

        //validate the file /config/settings.php
        $this->validateSettingsFile();

        return $next($request);
    }


    /**
     * Ensure the file /config/settings.php is valid and that
     * none of the required arrays are missing or have missing keys
     * @return void
     */
    private function validateSettingsFile() {

        //project statuses array
        $project_statuses = [
            'not_started',
            'in_progress',
            'on_hold',
            'cancelled',
            'completed',
        ];
        foreach ($project_statuses as $key) {
            if (!array_key_exists($key, config('settings.project_statuses'))) {
                Log::critical('/config/settings.php file error: (invalid $project_statuses array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }

        //invoice statuses array
        $invoice_statuses = [
            'draft',
            'due',
            'overdue',
            'paid',
            'part_paid',
        ];
        foreach ($invoice_statuses as $key) {
            if (!array_key_exists($key, config('settings.invoice_statuses'))) {
                Log::critical('/config/settings.php file error: (invalid $invoice_statuses array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }

        //estimate statuses array
        $estimate_statuses = [
            'draft',
            'new',
            'accepted',
            'declined',
            'revised',
            'expired',
        ];
        foreach ($estimate_statuses as $key) {
            if (!array_key_exists($key, config('settings.estimate_statuses'))) {
                Log::critical('/config/settings.php file error: (invalid $estimate_statuses array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }

        //task statuses array
        $task_priority = [
            'low',
            'normal',
            'high',
            'urgent',
        ];
        foreach ($task_priority as $key) {
            if (!array_key_exists($key, config('settings.task_priority'))) {
                Log::critical('/config/settings.php file error: (invalid $task_priority array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }

        //ticket statuses array
        $ticket_statuses = [
            'open',
            'on_hold',
            'answered',
            'closed',
        ];
        foreach ($ticket_statuses as $key) {
            if (!array_key_exists($key, config('settings.ticket_statuses'))) {
                Log::critical('/config/settings.php file error: (invalid $ticket_statuses array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }

        //ticket priority array
        $ticket_priority = [
            'normal',
            'high',
            'urgent',
        ];
        foreach ($ticket_priority as $key) {
            if (!array_key_exists($key, config('settings.ticket_priority'))) {
                Log::critical('/config/settings.php file error: (invalid $ticket_priority array)', ['process' => '[System Sanity Check]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                abort(409, __('lang.application_error'));
            }
        }
    }

}
