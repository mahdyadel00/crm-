<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for Clientlemailtemplates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\WebmailTemplates\CreateResponse;
use App\Http\Responses\Settings\WebmailTemplates\EditResponse;
use App\Http\Responses\Settings\WebmailTemplates\IndexResponse;
use App\Http\Responses\Settings\WebmailTemplates\StoreResponse;
use App\Http\Responses\Settings\WebmailTemplates\UpdateResponse;
use App\Http\Responses\Settings\WebmailTemplates\DestroyResponse;
use App\Repositories\WebmailTemplatesRepository;
use Illuminate\Http\Request;
use Validator;

class WebmailTemplates extends Controller {

    /**
     * The settings repository instance.
     */
    protected $templaterepo;

    public function __construct(WebmailTemplatesRepository $templaterepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings Webform
        $this->middleware('settingsMiddlewareIndex');

        $this->templaterepo = $templaterepo;

    }

    /**
     * Display Webform settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $templates = $this->templaterepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'templates' => $templates,
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
        $templates = $this->templaterepo->search($id);
        if (!$template = $templates->first()) {
            abort(404);
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'template' => $template,
        ];

        //show the form
        return new EditResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [
            'webmail_template_name.required' => __('lang.name') . ' - ' . __('lang.is_required'),
            'webmail_template_body.required' => __('lang.message') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'webmail_template_body' => [
                'required',
            ],
            'webmail_template_name' => [
                'required',
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

        //create the source
        if (!$template_id = $this->templaterepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the source object (friendly for rendering in blade template)
        $templates = $this->templaterepo->search($template_id);

        //count
        $count = \App\Models\WebmailTemplate::get()->count();

        //reponse payload
        $payload = [
            'templates' => $templates,
            'template_id' => $template_id,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [
            'webmail_template_name.required' => __('lang.name') . ' - ' . __('lang.is_required'),
            'webmail_template_body.required' => __('lang.message') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'webmail_template_body' => [
                'required',
            ],
            'webmail_template_name' => [
                'required',
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

        //get the form
        $templates = $this->templaterepo->search($id);
        if (!$template = $templates->first()) {
            abort(404);
        }

        //update
        $template->webmail_template_name = request('webmail_template_name');
        $template->webmail_template_body = request('webmail_template_body');
        $template->save();

        //get refreshed
        $templates = $this->templaterepo->search($id);

        //reponse payload
        $payload = [
            'templates' => $templates,
            'template_id' => $id,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the foo
        if (!$templates = $this->templaterepo->search($id)) {
            abort(409);
        }

        //remove the foo
        $templates->first()->delete();

        //reponse payload
        $payload = [
            'template_id' => $id,
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
                __('lang.webmail'),
                __('lang.templates'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.new_template'),
            'add_modal_create_url' => url('settings/webmail/templates/create'),
            'add_modal_action_url' => url('settings/webmail/templates'),
            'add_modal_action_ajax_class' => '',
            'add_modal_size' => 'modal-xl',
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
