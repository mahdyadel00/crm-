<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for milestones settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Milestones\CategoriesResponse;
use App\Http\Responses\Settings\Milestones\CreateResponse;
use App\Http\Responses\Settings\Milestones\DestroyCategoryResponse;
use App\Http\Responses\Settings\Milestones\EditCategoryResponse;
use App\Http\Responses\Settings\Milestones\IndexResponse;
use App\Http\Responses\Settings\Milestones\StoreCategoryResponse;
use App\Http\Responses\Settings\Milestones\UpdateCategoryResponse;
use App\Http\Responses\Settings\Milestones\UpdateResponse;
use App\Repositories\MilestoneCategoryRepository;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Validator;

class Milestones extends Controller {

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
        if (!$this->settingsrepo->updateMilestones()) {
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
    public function categories(MilestoneCategoryRepository $categoryrepo) {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $milestones = $categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'milestones' => $milestones,
        ];

        //show the view
        return new CategoriesResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function storeCategory(MilestoneCategoryRepository $categoryrepo) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'milestonecategory_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\MilestoneCategory::where('milestonecategory_title', $value)
                        ->exists()) {
                        return $fail(__('lang.milestone_already_exists'));
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
        if ($last = \App\Models\MilestoneCategory::orderBy('milestonecategory_position', 'desc')->first()) {
            $position = $last->milestonecategory_position + 1;
        } else {
            //default position
            $position = 2;
        }

        //counting current rows
        $rows = $categoryrepo->search();
        $count = $rows->total();

        //create the source
        if (!$milestonecategory_id = $categoryrepo->create($position)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $milestones = $categoryrepo->search($milestonecategory_id);

        //reponse payload
        $payload = [
            'milestones' => $milestones,
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

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @url baseusr/items/1/edit
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function editCategory(MilestoneCategoryRepository $categoryrepo, $id) {

        //page settings
        $page = $this->pageSettings('edit');

        //client leadsources
        $milestones = $categoryrepo->search($id);

        //not found
        if (!$milestone = $milestones->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'milestone' => $milestone,
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
    public function updateCategory(MilestoneCategoryRepository $categoryrepo, $id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'milestonecategory_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\MilestoneCategory::where('milestonecategory_title', $value)
                        ->where('milestonecategory_id', '!=', request()->route('id'))
                        ->exists()) {
                        return $fail(__('lang.milestone_already_exists'));
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

        //get the category object (friendly for rendering in blade template)
        $milestones = $categoryrepo->search($id);

        //reponse payload
        $payload = [
            'milestones' => $milestones,
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
        if (!$milestone = \App\Models\MilestoneCategory::find($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //delete the category
        $milestone->delete();

        //reponse payload
        $payload = [
            'milestone_id' => $id,
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
        foreach (request('sort-milestones') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\MilestoneCategory::where('milestonecategory_id', $id)->update(['milestonecategory_position' => $i]);
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
                __('lang.milestones'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => ' - ' . __('lang.milestones'),
            'heading' => __('lang.settings'),
            'settingsmenu_general' => 'active',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_milestone_category'),
            'add_modal_create_url' => url('settings/milestones/create'),
            'add_modal_action_url' => url('settings/milestones/create'),
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
