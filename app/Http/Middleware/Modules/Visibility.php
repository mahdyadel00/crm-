<?php

/** --------------------------------------------------------------------------------
 * This middleware mostly used for cluster/group menu visibility or other complex
 * menu structures. Regular menu items willjust used the modules.xvy check
 *
 * module visibility is set in [Middleware/Modules/Status.php]
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Modules;
use Closure;

class Visibility {

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //only logged in
        if (!auth()->check()) {
            return $next($request);
        }

        //set all the menus
        $this->viewProjects();
        $this->viewClients();
        $this->viewUsers();
        $this->viewInvoices();
        $this->viewTasks();
        $this->viewLeads();
        $this->viewPayments();
        $this->viewEstimates();
        $this->viewProducts();
        $this->viewExpenses();
        $this->viewSubscriptions();
        $this->viewTickets();
        $this->viewKnowledgebase();
        $this->viewTeam();
        $this->viewTimesheets();
        $this->viewTimetracking();
        $this->viewReminders();
        $this->viewProposals();
        $this->viewContracts();

        //done
        return $next($request);
    }

    /**
     * visibility of the projects features [both]
     */
    public function viewProjects() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_projects >= 1) {
                if (config('modules.projects')) {
                    config(['visibility.modules.projects' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.projects')) {
                config(['visibility.modules.projects' => true]);
            }
        }
    }

    /**
     * visibility of the client feature [team]
     */
    public function viewClients() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_clients >= 1) {
                config(['visibility.modules.clients' => true]);
            }
        }
    }

    /**
     * visibility of the client users features [team]
     */
    public function viewUsers() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_contacts >= 1) {
                config(['visibility.modules.users' => true]);
            }
        }
    }

    /**
     * visibility of the tasks feature [team]
     */
    public function viewTasks() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tasks >= 1) {
                if (config('modules.tasks') && config('modules.projects')) {
                    config(['visibility.modules.tasks' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.tasks')) {
                config(['visibility.modules.tasks' => true]);
            }
        }

    }

    /**
     * visibility of the leads feature [team]
     */
    public function viewLeads() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_leads >= 1) {
                if (config('modules.leads')) {
                    config(['visibility.modules.leads' => true]);
                }
            }
        }
    }

    /**
     * visibility of the invoices feature [both]
     */
    public function viewInvoices() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_invoices >= 1) {
                if (config('modules.invoices')) {
                    config(['visibility.modules.invoices' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.invoices')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.invoices' => true]);
                }
            }
        }
    }

    /**
     * visibility of the payments feature [both]
     */
    public function viewPayments() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_payments >= 1) {
                if (config('modules.payments')) {
                    config(['visibility.modules.payments' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.payments')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.payments' => true]);
                }
            }
        }
    }

    /**
     * visibility of the estimates feature [both]
     */
    public function viewEstimates() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_estimates >= 1) {
                if (config('modules.estimates')) {
                    config(['visibility.modules.estimates' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.estimates')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.estimates' => true]);
                }
            }
        }
    }

    /**
     * visibility of the products feature [team]
     */
    public function viewProducts() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_items >= 1) {
                //check against invoices module
                if (config('modules.invoices')) {
                    config(['visibility.modules.products' => true]);
                }
            }
        }
    }

    /**
     * visibility of the expenses feature [both]
     */
    public function viewExpenses() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_expenses >= 1) {
                if (config('modules.expenses')) {
                    config(['visibility.modules.expenses' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.expenses')) {
                config(['visibility.modules.expenses' => true]);
            }
        }

    }

    /**
     * visibility of the subscriptions feature [both]
     */
    public function viewSubscriptions() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_subscriptions >= 1) {
                if (config('modules.subscriptions')) {
                    config(['visibility.modules.subscriptions' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.subscriptions')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.subscriptions' => true]);
                }
            }
        }
    }

    /**
     * visibility of the tickets feature [both]
     */
    public function viewTickets() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tickets >= 1) {
                if (config('modules.tickets')) {
                    config(['visibility.modules.tickets' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.tickets')) {
                config(['visibility.modules.tickets' => true]);
            }
        }
    }

    /**
     * visibility of the tickets feature [both]
     */
    public function viewKnowledgebase() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_knowledgebase >= 1) {
                if (config('modules.knowledgebase')) {
                    config(['visibility.modules.knowledgebase' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.knowledgebase')) {
                config(['visibility.modules.knowledgebase' => true]);
            }
        }
    }

    /**
     * visibility of the team feature [team]
     */
    public function viewTeam() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_team >= 1) {
                config(['visibility.modules.team' => true]);
            }
        }
    }

    /**
     * visibility of the timesheets feature [team]
     */
    public function viewTimesheets() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_timesheets >= 1) {
                if (config('modules.timetracking')) {
                    config(['visibility.modules.timesheets' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.timetracking')) {
                config(['visibility.modules.timesheets' => true]);
            }
        }
    }

    /**
     * visibility of the time tracking (timers) feature [team]
     */
    public function viewTimetracking() {

        //team
        if (auth()->user()->is_team) {
            if (config('modules.timetracking')) {
                config(['visibility.modules.timetracking' => true]);
            }
        }
    }

    /**
     * visibility of reminders feature [team]
     */
    public function viewReminders() {

        //team
        if (config('modules.reminders')) {
            config(['visibility.modules.reminders' => true]);
        }
    }

    /**
     * visibility of the proposals feature [both]
     */
    public function viewProposals() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_proposals >= 1) {
                if (config('modules.proposals')) {
                    config(['visibility.modules.proposals' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.proposals')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.proposals' => true]);
                }
            }
        }
    }

    /**
     * visibility of the contracts feature [both]
     */
    public function viewContracts() {

        //team
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_contracts >= 1) {
                if (config('modules.contracts')) {
                    config(['visibility.modules.contracts' => true]);
                }
            }
        }

        //client
        if (auth()->user()->is_client) {
            if (config('modules.contracts')) {
                if (auth()->user()->is_client_owner) {
                    config(['visibility.modules.contracts' => true]);
                }
            }
        }
    }
}
