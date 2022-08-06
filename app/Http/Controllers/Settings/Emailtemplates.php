<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for email template settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Http\Responses\Settings\EmailTemplates\IndexResponse;
use App\Http\Responses\Settings\EmailTemplates\ShowResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Validator;

class Emailtemplates extends Controller {

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
     * Display templates home page
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //templates by types
        $users = \App\Models\EmailTemplate::Where('emailtemplate_category', 'users')->get();
        $projects = \App\Models\EmailTemplate::Where('emailtemplate_category', 'projects')->get();
        $leads = \App\Models\EmailTemplate::Where('emailtemplate_category', 'leads')->get();
        $tasks = \App\Models\EmailTemplate::Where('emailtemplate_category', 'tasks')->get();
        $billing = \App\Models\EmailTemplate::Where('emailtemplate_category', 'billing')->get();
        $tickets = \App\Models\EmailTemplate::Where('emailtemplate_category', 'tickets')->get();
        $system = \App\Models\EmailTemplate::Where('emailtemplate_category', 'system')->get();
        $other = \App\Models\EmailTemplate::Where('emailtemplate_category', 'other')->get();
        $estimates = \App\Models\EmailTemplate::Where('emailtemplate_category', 'estimates')->get();
        $subscriptions = \App\Models\EmailTemplate::Where('emailtemplate_category', 'subscriptions')->get();
        $modules = \App\Models\EmailTemplate::Where('emailtemplate_category', 'modules')->get();
        $proposals = \App\Models\EmailTemplate::Where('emailtemplate_category', 'proposals')->get();
        $contracts = \App\Models\EmailTemplate::Where('emailtemplate_category', 'contracts')->get();


        //reponse payload
        $payload = [
            'page' => $page,
            'users' => $users,
            'projects' => $projects,
            'leads' => $leads,
            'tasks' => $tasks,
            'billing' => $billing,
            'estimates' => $estimates,
            'subscriptions' => $subscriptions,
            'tickets' => $tickets,
            'system' => $system,
            'other' => $other,
            'modules' => $modules,
            'proposals' =>$proposals,
            'contracts' =>$contracts
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Display email template form
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_id', $id)->first()) {
            abort(404);
        }

        //basic variables
        $variables['template'] = explode(',', $template->emailtemplate_variables);
        $variables['general'] = explode(',', config('system.settings_email_general_variables'));

        //reponse payload
        $payload = [
            'template' => $template,
            'variables' => $variables,
        ];

        //show the view
        return new ShowResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //get the tempate
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_id', $id)->first()) {
            abort(404);
        }
        //validate
        $validator = Validator::make(request()->all(), [
            'emailtemplate_body' => 'required',
            'emailtemplate_subject' => 'required',
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
        $template->emailtemplate_subject = request('emailtemplate_subject');
        $template->emailtemplate_body = request('emailtemplate_body');
        $template->emailtemplate_status = (request('emailtemplate_status') == 'on') ? 'enabled' : 'disabled';

        $template->save();

        //reponse payload
        $payload = [
            'type' => 'success-notification',
        ];

        //generate a response
        return new CommonResponse($payload);
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
                __('lang.email'),
                __('lang.email_templates'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_email' => 'active',
        ];
        return $page;
    }

}
