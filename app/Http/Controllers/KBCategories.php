<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\KBCategories\CreateResponse;
use App\Http\Responses\KBCategories\DestroyResponse;
use App\Http\Responses\KBCategories\EditResponse;
use App\Http\Responses\KBCategories\IndexResponse;
use App\Http\Responses\KBCategories\StoreResponse;
use App\Http\Responses\KBCategories\UpdateResponse;
use App\Repositories\KbCategoryRepository;
use Illuminate\Http\Request;
use Validator;

class KBCategories extends Controller {

    /**
     * The category repository instance.
     */
    protected $categoryrepo;

    public function __construct(KbCategoryRepository $categoryrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('knowledgebaseMiddlewareIndex')->only(['index']);
        $this->middleware('knowledgebaseCategoriesMiddlewareEdit')->only(['edit', 'update']);
        $this->middleware('knowledgebaseCategoriesMiddlewareDestroy')->only(['destroy']);

        $this->categoryrepo = $categoryrepo;
    }

    /**
     * Display a listing of knowledgebase (category listing)
     * @return blade view | ajax view
     */
    public function index() {

        //basic page settings
        $page = $this->pageSettings('knowledgebase');

        //filter for normal users
        if (auth()->user()->role->role_knowledgebase < 3) {

            if (auth()->user()->type == 'team') {
                request()->merge([
                    'filter_user_type' => 'team',
                ]);
            }
            if (auth()->user()->type == 'client') {
                request()->merge([
                    'filter_user_type' => 'client',
                ]);
            }
        }

        //get categories
        $categories = $this->categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new category
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
     * Store a newly created category in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $validator = Validator::make(request()->all(), [
            'category_description' => 'required',
            'category_visibility' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['everyone', 'team', 'client'])) {
                        return $fail(__('lang.error_request_could_not_be_completed'));
                    }
                },
            ],
            'category_name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\Category::where('category_name', request('category_name'))
                        ->where('category_type', 'knowledgebase')
                        ->exists()) {
                        return $fail(__('lang.category_already_exists'));
                    }
                },
            ],
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //modify request
        request()->merge([
            'category_type' => 'knowledgebase',
        ]);

        //create the category
        if (!$category_id = $this->categoryrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the category object (friendly for rendering in blade template)
        $categories = $this->categoryrepo->search($category_id);

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified category
     * @param int $id category id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the item
        $category = \App\Models\Category::Where('category_id', $id)->first();

        //reponse payload
        $payload = [
            'page' => $page,
            'category' => $category,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified category in storage.
     * @param int $id category id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $validator = Validator::make(request()->all(), [
            'category_description' => 'required',
            'category_visibility' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, ['everyone', 'team', 'client'])) {
                        return $fail(__('lang.error_request_could_not_be_completed'));
                    }
                },
            ],
            'category_name' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\Category::where('category_name', request('category_name'))
                        ->where('category_type', 'knowledgebase')
                        ->where('category_id', '!=', request()->route('knowledgebase')) //the request id
                        ->exists()) {
                        return $fail(__('lang.category_already_exists'));
                    }
                },
            ],
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->categoryrepo->update($id)) {
            abort(409);
        }

        //get category
        $categories = $this->categoryrepo->search($id);

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified category from storage.
     * @param int $id category id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the category
        $knowledgebase = $this->categoryrepo->search($id);

        //remove the foo
        $knowledgebase->first()->delete();

        //reponse payload
        $payload = [
            'category_id' => $id,
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

        //common settings
        $page = [
            'page' => 'knowledgebase',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_kb' => 'active',
            'sidepanel_id' => 'sidepanel-filter-knowledgebase',
            'dynamic_search_url' => url('knowledgebase/search?action=search&knowledgebaseresource_id=' . request('knowledgebaseresource_id') . '&knowledgebaseresource_type=' . request('knowledgebaseresource_type')),
            'add_button_classes' => 'add-edit-foo-button',
            'load_more_button_route' => 'knowledgebase',
            'source' => 'list',
        ];

        //knowledgebase list page
        if ($section == 'knowledgebase') {
            $page += [
                'meta_title' => __('lang.knowledgebase'),
                'heading' => __('lang.knowledgebase'),
                'include_icons_selector_overlay' => 'yes',
                'add_modal_title' => __('lang.add_category'),
                'add_modal_create_url' => url('knowledgebase/create'),
                'add_modal_action_url' => url('knowledgebase'),
                'add_modal_action_ajax_class' => 'js-ajax-ux-request',
                'add_modal_action_ajax_loading_target' => 'commonModalBody',
                'add_modal_action_method' => 'POST',
            ];

            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //return
        return $page;
    }
}