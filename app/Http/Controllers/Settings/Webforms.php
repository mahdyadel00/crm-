<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for webforms
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Webforms\CodeResponse;
use App\Http\Responses\Settings\Webforms\CreateResponse;
use App\Http\Responses\Settings\Webforms\EditorResponse;
use App\Http\Responses\Settings\Webforms\IndexResponse;
use App\Http\Responses\Settings\Webforms\StoreResponse;
use App\Http\Responses\Settings\Webforms\DestroyResponse;
use App\Repositories\WebformRepository;
use Illuminate\Http\Request;
use Validator;

class Webforms extends Controller {

    /**
     * The settings repository instance.
     */
    protected $webformrepo;

    public function __construct(WebformRepository $webformrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings Webform
        $this->middleware('settingsMiddlewareIndex');

        $this->webformrepo = $webformrepo;

    }

    /**
     * Display Webform settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $webforms = $this->webformrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'webforms' => $webforms,
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
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('create');

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new EditorResponse($payload);
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
            'webform_title' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (\App\Models\Webform::where('webform_title', $value)
                        ->exists()) {
                        return $fail(__('lang.web_form_exists'));
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
        if (!$webforms_id = $this->webformrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $webforms = $this->webformrepo->search($webforms_id);

        //reponse payload
        $payload = [
            'webforms' => $webforms,
            'webforms_id' => $webforms_id
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function embedCode() {

        //page settings
        $page = $this->pageSettings('create');

        //code
        $code = '<iframe>Foo</iframe>';

        //reponse payload
        $payload = [
            'page' => $page,
            'code' => $code,
        ];

        //show the form
        return new CodeResponse($payload);
    }


        /**
     * Remove the specified resource from storage.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the foo
        if (!$webforms = $this->webformrepo->search($id)) {
            abort(409);
        }

        //remove the foo
        $webforms->first()->delete();

        //reponse payload
        $payload = [
            'id' => $id,
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
                __('lang.web_forms'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.new_web_form'),
            'add_modal_create_url' => url('settings/webforms/create'),
            'add_modal_action_url' => url('settings/webforms'),
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
