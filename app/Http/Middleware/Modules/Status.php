<?php

/** --------------------------------------------------------------------------------
 * This middleware set the global status of each module. Save the bool data in config
 *
 * [example] config('module.settings_modules_projects')
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Modules;
use Closure;

class Status {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //only for logged in users
        if (!auth()->check()) {
            return $next($request);
        }

        //valid modules (as found in the settings table)
        $modules = [
            'projects' => 'settings_modules_projects',
            'tasks' => 'settings_modules_tasks',
            'invoices' => 'settings_modules_invoices',
            'payments' => 'settings_modules_payments',
            'knowledgebase' => 'settings_modules_knowledgebase',
            'estimates' => 'settings_modules_estimates',
            'expenses' => 'settings_modules_expenses',
            'subscriptions' => 'settings_modules_subscriptions',
            'leads' => 'settings_modules_leads',
            'contracts' => 'settings_modules_contracts',
            'proposals' => 'settings_modules_proposals',
            'tickets' => 'settings_modules_tickets',
            'timetracking' => 'settings_modules_timetracking',
            'reminders' => 'settings_modules_reminders',
        ];

        //set theglobal visibility of each module
        foreach ($modules as $key => $value) {
            config(
                [
                    "modules.$key" => $this->moduleStatus($value),
                ]);
        }

        //done
        return $next($request);
    }

    /** -------------------------------------------------------------------------
     * Check the global and/or client modules status (enabled or disabled)
     * @param string $module the module that is being checked. The name corresponds
     *               with the field name, in the table, settings
     * @return bool
     * -------------------------------------------------------------------------*/
    public function moduleStatus($module) {

        //module is globallu disabled
        if (!config("system.$module") || config("system.$module") == 'disabled') {
            return false;
        }

        //module is disabled for client user (where client has been set to use custom modules)
        if (auth()->user()->is_client) {
            if (auth()->user()->client->client_app_modules == 'custom') {
                switch ($module) {
                case 'settings_modules_projects':
                    return (auth()->user()->client->client_settings_modules_projects == 'enabled') ? true : false;
                    break;
                case 'settings_modules_invoices':
                    return (auth()->user()->client->client_settings_modules_invoices == 'enabled') ? true : false;
                    break;
                case 'settings_modules_payments':
                    return (auth()->user()->client->client_settings_modules_payments == 'enabled') ? true : false;
                    break;
                case 'settings_modules_knowledgebase':
                    return (auth()->user()->client->client_settings_modules_knowledgebase == 'enabled') ? true : false;
                    break;
                case 'settings_modules_estimates':
                    return (auth()->user()->client->client_settings_modules_estimates == 'enabled') ? true : false;
                    break;
                case 'settings_modules_subscriptions':
                    return (auth()->user()->client->client_settings_modules_subscriptions == 'enabled') ? true : false;
                    break;
                case 'settings_modules_tickets':
                    return (auth()->user()->client->client_settings_modules_tickets == 'enabled') ? true : false;
                    break;
                }
            }
        }

        //finally
        return true;

    }

}