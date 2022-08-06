<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for roles settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Roles\CreateResponse;
use App\Http\Responses\Settings\Roles\DestroyResponse;
use App\Http\Responses\Settings\Roles\EditResponse;
use App\Http\Responses\Settings\Roles\IndexResponse;
use App\Http\Responses\Settings\Roles\StoreResponse;
use App\Http\Responses\Settings\Roles\UpdateResponse;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;
use Validator;

class Roles extends Controller {

    /**
     * The roles repository instance.
     */
    protected $rolesrepo;

    public function __construct(RoleRepository $rolesrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        //demo check
        $this->middleware('demoModeCheck')->only([
            'destroy',
        ]);

        $this->rolesrepo = $rolesrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //team roles only
        request()->merge([
            'filter_role_type' => 'team',
        ]);

        $roles = $this->rolesrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'roles' => $roles,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //page settings
        $page = $this->pageSettings('create');

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //validate
        $validator = Validator::make(request()->all(), [
            'role_name' => 'required',
            'role_clients' => 'required',
            'role_contacts' => 'required',
            'role_invoices' => 'required',
            'role_estimates' => 'required',
            'role_items' => 'required',
            'role_tasks' => 'required',
            'role_projects' => 'required',
            'role_leads' => 'required',
            'role_expenses' => 'required',
            'role_timesheets' => 'required',
            'role_tickets' => 'required',
            'role_knowledgebase' => 'required',
            'role_reports' => 'required',
            'role_assign_projects' => 'required',
            'role_assign_leads' => 'required',
            'role_assign_tasks' => 'required',
            'role_contracts' => 'required',
            'role_proposals' => 'required',
        ]);

        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //check duplicates
        if (\App\Models\Role::where('role_name', request('role_name'))
            ->exists()) {
            abort(409, __('lang.role_already_exists'));
        }

        //create the role
        if (!$role_id = $this->rolesrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $roles = $this->rolesrepo->search($role_id);

        //reponse payload
        $payload = [
            'roles' => $roles,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified resource.
     * @url baseusr/items/1/edit
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //client leadroles
        $roles = $this->rolesrepo->search($id);

        //not found
        if (!$role = $roles->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'role' => $role,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //validate
        $validator = Validator::make(request()->all(), [
            'role_name' => 'required',
            'role_clients' => 'required',
            'role_contacts' => 'required',
            'role_invoices' => 'required',
            'role_estimates' => 'required',
            'role_items' => 'required',
            'role_tasks' => 'required',
            'role_projects' => 'required',
            'role_templates_projects' => 'required',
            'role_leads' => 'required',
            'role_expenses' => 'required',
            'role_timesheets' => 'required',
            'role_tickets' => 'required',
            'role_knowledgebase' => 'required',
            'role_reports' => 'required',
            'role_assign_projects' => 'required',
            'role_assign_leads' => 'required',
            'role_assign_tasks' => 'required',
        ]);

        if ($validator->fails()) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        //check duplicates
        if (\App\Models\Role::where('role_name', request('role_name'))
            ->where('role_id', '!=', $id)
            ->exists()) {
            abort(409, __('lang.role_already_exists'));
        }

        //update the resource
        if (!$this->rolesrepo->update($id)) {
            abort(409);
        }

        //get the role object (friendly for rendering in blade template)
        $roles = $this->rolesrepo->search($id);

        //reponse payload
        $payload = [
            'roles' => $roles,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get record
        if (!\App\Models\Role::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get it in useful format
        $roles = $this->rolesrepo->search($id);
        $role = $roles->first();

        //validation: default
        if ($role->role_system == 'yes') {
            abort(409, __('lang.you_cannot_delete_system_default_item'));
        }

        //validation: default
        if ($role->count_users != 0) {
            abort(409, __('lang.role_not_empty'));
        }

        //delete the role
        $role->delete();

        //reponse payload
        $payload = [
            'role_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.user_roles'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_role'),
            'add_modal_create_url' => url('settings/roles/create'),
            'add_modal_action_url' => url('settings/roles'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        config([
            //visibility - add project buttton
            'visibility.list_page_actions_add_button' => true,
        ]);

        return $page;
    }

}
