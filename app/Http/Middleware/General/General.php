<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles precheck processes for general processes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\General;
use Cache;
use Closure;

class General {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        /* -
        ---------------------------------------------------------
         * [SETUP CHECK]
         * do not run this middleware is setup has not been completed
         * ----------------------------------------------------------*/
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return $next($request);
        }

        //user roles
        $this->userRoles();

        //last seen
        $this->lastSeen();

        //client user sanity
        $this->clientUser();

        //sanitize various request data
        $this->sanitizeRequest();

        //frontend settings
        $this->frontEnd();

        //set the language
        $this->setLanguage();

        //team members running timer
        $this->myTimer();

        //everyne members check duereminder
        $this->myReminders();

        //check if clients are allowed to login to the system
        if (auth()->check() && auth()->user()->is_client) {
            if (config('system.settings_clients_app_login') != 'enabled') {
                if (request()->ajax()) {
                    abort(409, __('lang.clients_disabled_login_error'));
                    return $next($request);
                } else {
                    request()->session()->flash('error-notification-long', __('lang.clients_disabled_login_error'));
                    auth()->logout();
                    abort(409, __('lang.clients_disabled_login_error'));
                    return $next($request);
                }
            }
        }

        //check account status
        if (auth()->check()) {
            if (auth()->user()->status != 'active') {
                if (request()->ajax()) {
                    abort(409, __('lang.account_has_been_suspended'));
                    return $next($request);
                } else {
                    request()->session()->flash('error-notification-long', __('lang.account_has_been_suspended'));
                    auth()->logout();
                    abort(409, __('lang.account_has_been_suspended'));
                    return $next($request);
                }
            }
        }

        //get all categories that a user is assigned to
        if (auth()->check()) {
            $categories = \App\Models\Category::Where('category_type', 'project')->orderBy('category_name', 'asc')->get();
            $projects_menu_list = [];
            foreach ($categories as $category) {
                //category users
                $users = $category->users;
                if ($users->contains('id', auth()->user()->id) || auth()->user()->is_admin) {
                    $projects_menu_list[] = $category->category_id;
                }
            }
            request()->merge([
                'projects_menu_list' => $projects_menu_list,
            ]);
        }

        return $next($request);
    }

    /**
     * sanitize various request data
     * @return void
     */
    private function setTimezone() {

    }

    /**
     * sanitize various request data
     * @return void
     */
    private function sanitizeRequest() {
        /**
         * sanitize invoice & estimate id's
         *      - remove prefixes
         *      - remove none numeric (except .,)
         *      - remove leading zeros
         * */
        if (request()->filled('payment_invoiceid')) {
            $payment_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('payment_invoiceid'));
            $payment_invoiceid = preg_replace("/[^0-9]/", '', $payment_invoiceid);
            $payment_invoiceid = ltrim($payment_invoiceid, '0');
            request()->merge(['payment_invoiceid' => $payment_invoiceid]);
        }
        if (request()->filled('bill_invoiceid')) {
            $bill_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('bill_invoiceid'));
            $bill_invoiceid = preg_replace("/[^0-9]/", '', $bill_invoiceid);
            $bill_invoiceid = ltrim($bill_invoiceid, '0');
            request()->merge(['bill_invoiceid' => $bill_invoiceid]);
        }
        if (request()->filled('filter_bill_invoiceid')) {
            $filter_bill_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('filter_bill_invoiceid'));
            $filter_bill_invoiceid = preg_replace("/[^0-9]/", '', $filter_bill_invoiceid);
            $filter_bill_invoiceid = ltrim($filter_bill_invoiceid, '0');
            request()->merge(['filter_bill_invoiceid' => $filter_bill_invoiceid]);
        }
        if (request()->filled('filter_payment_invoiceid')) {
            $filter_payment_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('filter_payment_invoiceid'));
            $filter_payment_invoiceid = preg_replace("/[^0-9]/", '', $filter_payment_invoiceid);
            $filter_payment_invoiceid = ltrim($filter_payment_invoiceid, '0');
            request()->merge(['bill_invoiceid' => $filter_payment_invoiceid]);
        }
        if (request()->filled('bill_estimateid')) {
            $bill_estimateid = str_replace(config('system.settings_estimates_prefix'), '', request('bill_estimateid'));
            $bill_estimateid = preg_replace("/[^0-9]/", '', $bill_estimateid);
            $bill_estimateid = ltrim($bill_estimateid, '0');
            request()->merge(['bill_estimateid' => $bill_estimateid]);
        }
        if (request()->filled('filter_bill_estimateid')) {
            $filter_bill_estimateid = str_replace(config('system.settings_estimates_prefix'), '', request('filter_bill_estimateid'));
            $filter_bill_estimateid = preg_replace("/[^0-9]/", '', $filter_bill_estimateid);
            $filter_bill_estimateid = ltrim($filter_bill_estimateid, '0');
            request()->merge(['filter_bill_estimateid' => $filter_bill_estimateid]);
        }
        return;
    }

    /**
     * Preload user model relationships
     * @return void
     */
    private function userRoles() {
        if (auth()->check()) {
            //load useers roles
            auth()->user()->load('role');
            auth()->user()->load('client');
        }
    }

    /**
     * apply database prefilters for all client users
     * @return void
     */
    private function lastSeen() {
        //add tome when last seen
        if (auth()->check()) {
            //update user last neen and ip address
            $user = auth()->user();
            $user->last_seen = \Carbon\Carbon::now();
            $user->last_ip_address = request()->ip();
            $user->save();
        }
        //cache the users online status
        if (auth()->check()) {
            $expiresAt = \Carbon\Carbon::now()->addMinutes(3);
            Cache::put('user-is-online-' . auth()->user()->id, true, $expiresAt);
        }
    }

    /**
     * apply database prefilters for all client users
     * @return void
     */
    private function clientUser() {
        if (auth()->check() && auth()->user()->is_client) {
            $clientid = auth()->user()->clientid;
            request()->merge([
                'clientid' => $clientid,
                'filter_contract_clientid' => $clientid,
                'filter_bill_clientid' => $clientid,
                'filter_expense_clientid' => $clientid,
                'filter_file_clientid' => $clientid,
                'filter_bill_clientid' => $clientid,
                'filter_payment_clientid' => $clientid,
                'filter_project_clientid' => $clientid,
                'filter_task_clientid' => $clientid,
                'filter_timer_clientid' => $clientid,
                'filter_ticket_clientid' => $clientid,
                'filter_subscription_clientid' => $clientid,
                'filter_contract_clientid' => $clientid,
                'filter_proposal_clientid' => $clientid,
            ]);
        }
    }

    /**
     * various frontend settings
     * @return void
     */
    private function frontEnd() {

        //left menu hamburger
        request()->merge([
            'visibility_left_menu_toggle_button' => 'visible',
        ]);

    }

    /**
     * set the language to be used by the app
     * @return void
     */
    private function setLanguage() {

        //set default system language first
        $lang = config('system.settings_system_language_default');
        if (file_exists(resource_path("lang/$lang"))) {
            //for use by javascripts - like tinymce (set in the header)
            request()->merge([
                'system_language' => $lang,
            ]);
            \App::setLocale($lang);
        } else {
            //for use by javascripts - like tinymce (set in the header)
            request()->merge([
                'system_language' => 'english',
            ]);
            //revert to english
            \App::setLocale('english');
        }

        //set users language (if app settings allow it)
        if (auth()->check() && config('system.settings_system_language_allow_users_to_change') == 'yes') {
            $lang = auth()->user()->pref_language;
            if (file_exists(resource_path("lang/$lang"))) {
                \App::setLocale($lang);
            }
        }

        //create list of languages
        $dir = BASE_DIR . '/application/resources/lang';
        $languages = array_diff(scandir($dir), array('..', '.'));
        request()->merge([
            'system_languages' => $languages,
        ]);
    }

    /**
     * check if user has a running timer
     * @return void
     */
    private function myTimer() {

        //only logged in users
        if (!auth()->check()) {
            return;
        }

        //only team members
        if (auth()->user()->is_client) {
            return;
        }

        //check if I have a running timer
        if (!$timer = \App\Models\Timer::where('timer_status', 'running')->where('timer_creatorid', auth()->id())->first()) {
            return;
        }

        //get the task
        if ($task = \App\Models\Task::where('task_id', $timer->timer_taskid)->first()) {
            $task_title = $task->task_title;
            $task_id = $task->task_id;
            $task = true;
        } else {
            $task_title = '';
            $task_id = '';
            $task = true;
        }

        //make the counter visible
        request()->merge([
            'show_users_running_timer' => 'show',
            'users_running_timer' => time() - $timer->timer_started,
            'users_running_timer_task_title' => $task_title,
            'users_running_timer_task_id' => $task_id,
            'users_running_timer_task' => $task,
        ]);

    }

    /**
     * check if user has any due reminders
     * @return void
     */
    private function myReminders() {

        //only logged in users
        if (!auth()->check()) {
            return;
        }

        //default (hide icon)
        request()->merge([
            'user_has_due_reminder' => 'hidden',
        ]);

        if (\App\Models\Reminder::Where('reminder_status', 'due')->where('reminder_userid', auth()->id())->exists()) {
            request()->merge([
                'user_has_due_reminder' => '',
            ]);
        }

    }

}