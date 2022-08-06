<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for sources settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Sources\CreateResponse;
use App\Http\Responses\Settings\Sources\DestroyResponse;
use App\Http\Responses\Settings\Sources\EditResponse;
use App\Http\Responses\Settings\Sources\IndexResponse;
use App\Http\Responses\Settings\Sources\StoreResponse;
use App\Http\Responses\Settings\Sources\UpdateResponse;
use App\Repositories\LeadSourcesRepository;
use Illuminate\Http\Request;
use Validator;

class Sources extends Controller {

    /**
     * The sources repository instance.
     */
    protected $sourcesrepo;

    public function __construct(LeadSourcesRepository $sourcesrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->sourcesrepo = $sourcesrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $sources = $this->sourcesrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'sources' => $sources,
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

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'leadsources_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\LeadSources::where('leadsources_title', $value)
                        ->exists()) {
                        return $fail(__('lang.source_already_exists'));
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

        //create the source
        if (!$leadsources_id = $this->sourcesrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $sources = $this->sourcesrepo->search($leadsources_id);

        //reponse payload
        $payload = [
            'sources' => $sources,
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

        //client leadsources
        $leadsources = $this->sourcesrepo->search($id);

        //not found
        if (!$leadsource = $leadsources->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'source' => $leadsource,
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

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'leadsources_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\LeadSources::where('leadsources_title', $value)
                        ->where('leadsources_id', '!=', request()->route('source'))
                        ->exists()) {
                        return $fail(__('lang.source_already_exists'));
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
        if (!$this->sourcesrepo->update($id)) {
            abort(409);
        }

        //get the category object (friendly for rendering in blade template)
        $sources = $this->sourcesrepo->search($id);

        //reponse payload
        $payload = [
            'sources' => $sources,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified resource from storage.
     * @url baseusr/sources/1
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the source
        if (!$sources = $this->sourcesrepo->search($id)) {
            abort(409);
        }

        //remove the source
        $sources->first()->delete();

        //reponse payload
        $payload = [
            'source_id' => $id,
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
                __('lang.leads'),
                __('lang.lead_sources'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_lead_source'),
            'add_modal_create_url' => url('settings/sources/create'),
            'add_modal_action_url' => url('settings/sources'),
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
