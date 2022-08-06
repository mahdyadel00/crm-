<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for estimates settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Knowledgebase\CategoriesResponse;
use App\Http\Responses\Settings\Knowledgebase\CreateResponse;
use App\Http\Responses\Settings\Knowledgebase\DestroyCategoryResponse;
use App\Http\Responses\Settings\Knowledgebase\EditCategoryResponse;
use App\Http\Responses\Settings\Knowledgebase\IndexResponse;
use App\Http\Responses\Settings\Knowledgebase\StoreCategoryResponse;
use App\Http\Responses\Settings\Knowledgebase\UpdateCategoryResponse;
use App\Http\Responses\Settings\Knowledgebase\UpdateResponse;
use App\Repositories\KbCategoryRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Validator;

class Knowledgebase extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //update
        if (!$this->settingsrepo->updateKnowledgebase()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function categories(KbCategoryRepository $categoryrepo) {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $categories = $categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'categories' => $categories,
        ];

        //show the view
        return new CategoriesResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(KbCategoryRepository $categoryrepo) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'kbcategory_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\KbCategories::where('kbcategory_title', $value)
                        ->exists()) {
                        return $fail(__('lang.category_already_exists'));
                    }
                }],
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

        //get the last row (order by position - desc)
        if ($last = \App\Models\KbCategories::orderBy('kbcategory_position', 'desc')->first()) {
            $position = $last->kbcategory_position + 1;
        } else {
            //default position
            $position = 2;
        }

        //counting current rows
        $rows = $categoryrepo->search();
        $count = $rows->total();

        //create the source
        if (!$category_id = $categoryrepo->create($position)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $category = $categoryrepo->search($categoryrepo);

        //reponse payload
        $payload = [
            'category' => $category,
            'count' => $count,
            'page' => $this->pageSettings(),
        ];

        //process reponse
        return new StoreCategoryResponse($payload);

    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
        ];

        //show category selection
        config(['visibility.select_category' => true]);

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @url baseusr/items/1/edit
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function editCategory(KbCategoryRepository $categoryrepo, $id) {

        //page settings
        $page = $this->pageSettings('edit');
        $page['section'] = 'edit';

        //client leadsources
        $categories = $categoryrepo->search($id);

        //not found
        if (!$category = $categories->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'category' => $category,
            'categories' => $categoryrepo->search(),
        ];

        //response
        return new EditCategoryResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateCategory(KbCategoryRepository $categoryrepo, $id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'kbcategory_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\KbCategories::where('kbcategory_title', $value)
                        ->where('kbcategory_id', '!=', request()->route('id'))
                        ->exists()) {
                        return $fail(__('lang.knowledgebase_already_exists'));
                    }
                }],
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

        //update the resource
        if (!$categoryrepo->update($id)) {
            abort(409);
        }

        //moving artciles
        if (is_numeric(request('migrate'))) {

            //get old category
            if(!$old = \App\Models\KbCategories::Where('kbcategory_id', $id)->first()){
                abort(409);
            }
            //get new category
            if(!$new = \App\Models\KbCategories::Where('kbcategory_id', request('migrate'))->first()){
                abort(409);
            }

            //make sure they are same type
            if($old->kbcategory_type != $new->kbcategory_type){
                abort(409, __('lang.moving_kb_categories_warning'));
            }

            \App\Models\Knowledgebase::where('knowledgebase_categoryid', $id)
                ->update(['knowledgebase_categoryid' => request('migrate')]);
        }

        //get the category object (friendly for rendering in blade template)
        $categories = $categoryrepo->search($id);

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //process reponse
        return new UpdateCategoryResponse($payload);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get record
        if (!$category = \App\Models\KbCategories::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        if ($category->count_articles > 0) {
            abort(409, __('lang.category_not_empty'));
        }

        //delete the category
        $category->delete();

        //reponse payload
        $payload = [
            'category_id' => $id,
        ];

        //process reponse
        return new DestroyCategoryResponse($payload);
    }

    /**
     * Update a stages position
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function updateCategoryPositions() {

        //reposition each lead status
        $i = 1;
        foreach (request('sort-categories') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\KbCategories::where('kbcategory_id', $id)->update(['kbcategory_position' => $i]);
            }
            $i++;
        }

        //retun simple success json
        return response()->json('success', 200);
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
                __('lang.knowledgebase'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => ' - ' . __('lang.knowledgebase'),
            'heading' => __('lang.settings'),
            'settingsmenu_general' => 'active',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_knowledgebase_category'),
            'add_modal_create_url' => url('settings/knowledgebase/create'),
            'add_modal_action_url' => url('settings/knowledgebase/create'),
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
