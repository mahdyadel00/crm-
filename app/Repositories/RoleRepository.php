<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for roles
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Role;
use Log;

class RoleRepository {

    /**
     * The roles repository instance.
     */
    protected $roles;

    /**
     * Inject dependecies
     */
    public function __construct(Role $roles) {
        $this->roles = $roles;
    }

    /**
     * Get all team roles
     * @return object
     */
    public function allTeamRoles() {
        return $this->roles->All()->whereNotIn('role_name', 'Client');
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object role collection
     */
    public function search($id = '') {

        $roles = $this->roles->newQuery();

        // all client fields
        $roles->selectRaw('*');

        //count users on this role
        $roles->selectRaw("(SELECT COUNT(*)
                                      FROM users
                                      WHERE role_id = roles.role_id
                                      AND status NOT IN('deleted'))
                                      AS count_users");

        if (is_numeric($id)) {
            $roles->where('role_id', $id);
        }

        //filter clients
        if (request()->filled('filter_role_type')) {
            $roles->where('role_type', request('filter_role_type'));
        }

        //default sorting
        //$roles->orderBy('role_id', 'desc');
        $roles->orderBy('role_name', 'asc');

        // Get the results and return them.
        return $roles->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $role = new $this->roles;

        //valid role values
        $valid = [0, 1, 2, 3];

        //data - for security,we will do some extra validations for each entry
        $role->role_name = ucwords(request('role_name'));
        $role->role_clients = (in_array(request('role_clients'), $valid)) ? request('role_clients') : 0;
        $role->role_contacts = (in_array(request('role_contacts'), $valid)) ? request('role_contacts') : 0;
        $role->role_invoices = (in_array(request('role_invoices'), $valid)) ? request('role_invoices') : 0;
        $role->role_payments = (in_array(request('role_payments'), $valid)) ? request('role_payments') : 0;
        $role->role_estimates = (in_array(request('role_estimates'), $valid)) ? request('role_estimates') : 0;
        $role->role_items = (in_array(request('role_items'), $valid)) ? request('role_items') : 0;
        $role->role_tasks = (in_array(request('role_tasks'), $valid)) ? request('role_tasks') : 0;
        $role->role_team = (in_array(request('role_team'), $valid)) ? request('role_team') : 0;
        $role->role_projects = (in_array(request('role_projects'), $valid)) ? request('role_projects') : 0;
        $role->role_leads = (in_array(request('role_leads'), $valid)) ? request('role_leads') : 0;
        $role->role_expenses = (in_array(request('role_expenses'), $valid)) ? request('role_expenses') : 0;
        $role->role_timesheets = (in_array(request('role_timesheets'), $valid)) ? request('role_timesheets') : 0;
        $role->role_tickets = (in_array(request('role_tickets'), $valid)) ? request('role_tickets') : 0;
        $role->role_knowledgebase = (in_array(request('role_knowledgebase'), $valid)) ? request('role_knowledgebase') : 0;
        $role->role_reports = (in_array(request('role_reports'), $valid)) ? request('role_reports') : 0;
        $role->role_assign_projects = (request('role_assign_projects') == 'yes') ? 'yes' : 'no';
        $role->role_assign_leads = (request('role_assign_leads') == 'yes') ? 'yes' : 'no';
        $role->role_assign_tasks = (request('role_assign_tasks') == 'yes') ? 'yes' : 'no';
        $role->role_tasks_scope = (request('role_tasks_scope') == 'on') ? 'global' : 'own';
        $role->role_projects_scope = (request('role_projects_scope') == 'on') ? 'global' : 'own';
        $role->role_leads_scope = (request('role_leads_scope') == 'on') ? 'global' : 'own';
        $role->role_expenses_scope = (request('role_expenses_scope') == 'on') ? 'global' : 'own';
        $role->role_timesheets_scope = (request('role_timesheets_scope') == 'on') ? 'global' : 'own';
        $role->role_contracts = (in_array(request('role_contracts'), $valid)) ? request('role_contracts') : 0;
        $role->role_proposals = (in_array(request('role_proposals'), $valid)) ? request('role_proposals') : 0;


        $role->role_type = 'team';

        //save and return id
        if ($role->save()) {
            return $role->role_id;
        } else {
            Log::error("record could not be created - database error", ['process' => '[RoleRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$role = $this->roles->find($id)) {
            return false;
        }

        //valid role values
        $valid = [0, 1, 2, 3];

        //data - for security,we will do some extra validations for each entry
        $role->role_name = ucwords(request('role_name'));
        $role->role_clients = (in_array(request('role_clients'), $valid)) ? request('role_clients') : 0;
        $role->role_contacts = (in_array(request('role_contacts'), $valid)) ? request('role_contacts') : 0;
        $role->role_invoices = (in_array(request('role_invoices'), $valid)) ? request('role_invoices') : 0;
        $role->role_payments = (in_array(request('role_payments'), $valid)) ? request('role_payments') : 0;
        $role->role_estimates = (in_array(request('role_estimates'), $valid)) ? request('role_estimates') : 0;
        $role->role_items = (in_array(request('role_items'), $valid)) ? request('role_items') : 0;
        $role->role_tasks = (in_array(request('role_tasks'), $valid)) ? request('role_tasks') : 0;
        $role->role_team = (in_array(request('role_team'), $valid)) ? request('role_team') : 0;
        $role->role_projects = (in_array(request('role_projects'), $valid)) ? request('role_projects') : 0;
        $role->role_templates_projects = (in_array(request('role_templates_projects'), $valid)) ? request('role_templates_projects') : 0;
        $role->role_leads = (in_array(request('role_leads'), $valid)) ? request('role_leads') : 0;
        $role->role_expenses = (in_array(request('role_expenses'), $valid)) ? request('role_expenses') : 0;
        $role->role_timesheets = (in_array(request('role_timesheets'), $valid)) ? request('role_timesheets') : 0;
        $role->role_tickets = (in_array(request('role_tickets'), $valid)) ? request('role_tickets') : 0;
        $role->role_knowledgebase = (in_array(request('role_knowledgebase'), $valid)) ? request('role_knowledgebase') : 0;
        $role->role_reports = (in_array(request('role_reports'), $valid)) ? request('role_reports') : 0;
        $role->role_assign_projects = (request('role_assign_projects') == 'yes') ? 'yes' : 'no';
        $role->role_assign_leads = (request('role_assign_leads') == 'yes') ? 'yes' : 'no';
        $role->role_assign_tasks = (request('role_assign_tasks') == 'yes') ? 'yes' : 'no';
        $role->role_tasks_scope = (request('role_tasks_scope') == 'on') ? 'global' : 'own';
        $role->role_projects_scope = (request('role_projects_scope') == 'on') ? 'global' : 'own';
        $role->role_leads_scope = (request('role_leads_scope') == 'on') ? 'global' : 'own';
        $role->role_expenses_scope = (request('role_expenses_scope') == 'on') ? 'global' : 'own';
        $role->role_timesheets_scope = (request('role_timesheets_scope') == 'on') ? 'global' : 'own';
        $role->role_manage_knowledgebase_categories = (request('role_manage_knowledgebase_categories') == 'yes') ? 'yes' : 'no';
        $role->role_set_project_permissions = (request('role_set_project_permissions') == 'yes') ? 'yes' : 'no';
        $role->role_contracts = (in_array(request('role_contracts'), $valid)) ? request('role_contracts') : 0;
        $role->role_proposals = (in_array(request('role_proposals'), $valid)) ? request('role_proposals') : 0;
        
        //save
        if ($role->save()) {
            return $role->role_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[RoleRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}