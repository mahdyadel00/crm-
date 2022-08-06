<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Categories\CreateResponse;
use App\Http\Responses\Categories\DestroyResponse;
use App\Http\Responses\Categories\EditResponse;
use App\Http\Responses\Categories\IndexResponse;
use App\Http\Responses\Categories\StoreResponse;
use App\Http\Responses\Categories\UpdateResponse;
use App\Repositories\CategoryRepository;
use App\Repositories\ProjectRepository;
use App\Rules\NoTags;
use Illuminate\Http\Request;
use Validator;

class Categories extends Controller {

    /**
     * The category repository instance.
     */
    protected $categoryrepo;

    /**
     * array of valid category types.
     */
    protected $resource_types;

    public function __construct(CategoryRepository $categoryrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //admin
        $this->middleware('adminCheck');

        //general
        $this->middleware('auth');

        $this->categoryrepo = $categoryrepo;

        //validate type (note: update this when you add new types)
        $this->resource_types = [
            'project',
            'client',
            'estimate',
            'ticket',
            'lead',
            'invoice',
            'contract',
            'expense',
            'knowledgebase',
            'item',
            'proposal',
            'contract',
        ];
    }

    /**
     * Display a listing of categories
     * @param object Request the request object
     * @return blade view | ajax view
     */
    public function index(Request $request) {

        //basic page settings
        $page = $this->pageSettings();

        //get categories
        $categories = $this->categoryrepo->search();

        $this->applyCount($categories);

        //reponse payload
        $payload = [
            'page' => $page,
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @param object CategoryRepository category repository instance
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

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
     * Store a newly created category in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'category_name' => [
                'required',
                new NoTags,
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        if (!in_array(request('category_type'), $this->resource_types)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //check duplicates
        if (\App\Models\Category::where('category_name', request('category_name'))
            ->where('category_type', request('category_type'))
            ->exists()) {
            abort(409, __('lang.category_already_exists'));
        }

        //create the category
        if (!$category_id = $this->categoryrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the category object (friendly for rendering in blade template)
        $categories = $this->categoryrepo->search($category_id);

        $this->applyCount($categories);

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id category id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');
        $page['section'] = 'edit';

        //client categories
        $categories = $this->categoryrepo->search($id);

        //not found
        if (!$category = $categories->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //get all categories of this type
        $categories = $this->categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'category' => $category,
            'categories' => $categories,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified category in storage.
     * @param  int  $id category id
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectRepository $projectrepo, $id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'category_name' => [
                'required',
                new NoTags,
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //check duplicates
        if (\App\Models\Category::where('category_name', request('category_name'))
            ->where('category_type', request('category_type'))
            ->where('category_id', '!=', $id)
            ->exists()) {
            abort(409, __('lang.category_already_exists'));
        }

        //update the category
        if (!$this->categoryrepo->update($id)) {
            abort(409);
        }

        //are we migrating content
        if (is_numeric(request('migrate')) && $category = \App\Models\Category::Where('category_id', request('migrate'))->first()) {

            /** -------------------------------------------------------------------------
             * [CATEGORY USERS]
             * un-assign all users from all the projects that were in this category
             * we will assign afresh later (below)
             * -------------------------------------------------------------------------*/
            $projectrepo->assignCategoryProjects($id);
            if (config('system.settings_projects_permissions_basis') == 'category_based') {
                $projectrepo->unassignCategoryProjects($id);
            }

            /** lets just update all possible tables (reasonable way to do it)*/

            //projects
            \App\Models\Project::where('project_categoryid', $id)
                ->update(['project_categoryid' => request('migrate')]);

            //clients
            \App\Models\Client::where('client_categoryid', $id)
                ->update(['client_categoryid' => request('migrate')]);

            //contracts
            \App\Models\Contract::where('doc_categoryid', $id)
                ->update(['doc_categoryid' => request('migrate')]);

            //expenses
            \App\Models\Expense::where('expense_categoryid', $id)
                ->update(['expense_categoryid' => request('migrate')]);

            //invoices
            \App\Models\Invoice::where('bill_categoryid', $id)
                ->update(['bill_categoryid' => request('migrate')]);

            //leads
            \App\Models\Lead::where('lead_categoryid', $id)
                ->update(['lead_categoryid' => request('migrate')]);

            //tickets
            \App\Models\Ticket::where('ticket_categoryid', $id)
                ->update(['ticket_categoryid' => request('migrate')]);

            //items
            \App\Models\Item::where('item_categoryid', $id)
                ->update(['item_categoryid' => request('migrate')]);

            //estimates
            \App\Models\Estimate::where('bill_categoryid', $id)
                ->update(['bill_categoryid' => request('migrate')]);

            /** -------------------------------------------------------------------------
             * [CATEGORY USERS]
             * Reassign all the projects that are now in this category with the new user
             * list for this category
             * -------------------------------------------------------------------------*/
            if (config('system.settings_projects_permissions_basis') == 'category_based') {
                $projectrepo->assignCategoryProjects(request('migrate'));
            }

        }

        //get updated categories for this type (filtered by url query)
        $categories = $this->categoryrepo->search();

        //apply count
        $this->applyCount($categories);

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified category from storage.
     * @param int $id category id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get record
        if (!\App\Models\Category::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get it in useful format
        $categories = $this->categoryrepo->search($id);
        $this->applyCount($categories);
        $category = $categories->first();

        //validation: default
        if ($category->category_system_default == 'yes') {
            abort(409, __('lang.you_cannot_delete_system_default_item'));
        }

        //validation: default
        if ($category->count != 0) {
            abort(409, __('lang.category_not_empty'));
        }

        //delete the category
        $category->delete();

        //delete category users
        \App\Models\CategoryUser::Where('categoryuser_categoryid', $id)->delete();

        //reponse payload
        $payload = [
            'category_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
    }

    /**
     * show list of team members
     *
     * @return \Illuminate\Http\Response
     */
    public function showTeam($id) {

        //get the users
        $users = \App\Models\CategoryUser::Where('categoryuser_categoryid', $id)->get();

        //page
        $html = view('pages/categories/modals/team', compact('users'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXCategoriesUsers',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateTeam(ProjectRepository $projectrepo, $id) {

        //get the category
        if (!$category = \App\Models\Category::Where('category_id', $id)->first()) {
            abort(404);
        }

        //delete current users
        \App\Models\CategoryUser::Where('categoryuser_categoryid', $id)->delete();

        //add all the posted users
        $count = 0;
        if (request()->filled('users')) {
            foreach (request('users') as $user) {
                $categoryuser = new \App\Models\CategoryUser();
                $categoryuser->categoryuser_categoryid = $id;
                $categoryuser->categoryuser_userid = $user;
                $categoryuser->save();
                $count++;
            }
        }

        /** -------------------------------------------------------------------------
         * [CATEGORY USERS]
         * Reassign all the projects that are in this category with the new user list
         * for this category
         * -------------------------------------------------------------------------*/
        $projectrepo->assignCategoryProjects($id);

        //notice error
        $jsondata['notification'] = [
            'type' => 'success',
            'value' => __('lang.request_has_been_completed'),
        ];

        //close modal
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModal', 'action' => 'close-modal',
        ];

        //update counter
        $jsondata['dom_html'][] = [
            'selector' => "#category_user_count_$id",
            'action' => 'replace',
            'value' => $count,
        ];

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * pass the categories object and apply correct count
     * @param object $categories categories model object
     * @return object
     */
    private function applyCount($categories = '') {

        //sanity - make sure this is a valid project object
        if ($categories instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            foreach ($categories as $category) {
                switch ($category->category_type) {
                case 'client':
                    $category->count = $category->count_clients;
                    break;
                case 'project':
                    $category->count = $category->count_projects;
                    break;
                case 'contract':
                    $category->count = $category->count_contracts;
                    break;
                case 'expense':
                    $category->count = $category->count_expenses;
                    break;
                case 'invoice':
                    $category->count = $category->count_invoices;
                    break;
                case 'lead':
                    $category->count = $category->count_leads;
                    break;
                case 'ticket':
                    $category->count = $category->count_tickets;
                    break;
                case 'item':
                    $category->count = $category->count_items;
                    break;
                case 'estimate':
                    $category->count = $category->count_estimates;
                    break;
                case 'proposal':
                    $category->count = $category->count_proposals;
                    break;
                case 'contract':
                    $category->count = $category->count_contracts;
                    break;
                default:
                    $category->count = 0;
                    break;
                }
            }
        }
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.categories'),
                runtimeLang(request('filter_category_type')),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'categories',
            'no_results_message' => __('lang.item_not_found'),
            'add_button_classes' => 'add-edit-invoice-button',
            'load_more_button_route' => 'categories',
            'meta_title' => __('lang.categories'),
            'heading' => __('lang.categories'),
            'source' => 'list',
            'form_label_category_name' => __('lang.category_name'),
            'form_label_move_items' => __('lang.move_to_another_category'),
        ];

        config([
            //visibility - add project buttton
            'visibility.list_page_actions_add_button' => true,
            //visibility - filter button
            'visibility.list_page_actions_filter_button' => false,
            //visibility - search field
            'visibility.list_page_actions_search' => false,
            //visibility - toggle stats
            'visibility.stats_toggle_button' => false,
            'visibility.left_menu_toggle_button' => true,
        ]);

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_category'),
            'add_modal_create_url' => url('categories/create?category_type=' . request('filter_category_type')),
            'add_modal_action_url' => url('categories'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
            'edit_modal_action_title' => __('lang.edit_category'),
        ];

        //ext page settings
        if (request('source') == 'ext') {
            $page += [
                'list_page_actions_size' => 'col-lg-12',
            ];
        }

        //[tickets] change settings for this type category
        if (request('filter_category_type') == 'ticket' || in_array($section, ['ticket', 'milestone'])) {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.tickets'),
                __('lang.departments'),
            ];
            $page['heading'] = __('lang.tickets');
            $page['add_modal_title'] = __('lang.add_department');
            $page['form_label_category_name'] = __('lang.department_name');
            $page['form_label_move_items'] = __('lang.move_tickets_to_another_department');
            $page['edit_modal_action_title'] = __('lang.edit_department');
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }

        //return
        return $page;
    }
}
