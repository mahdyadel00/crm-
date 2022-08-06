<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Webmail;

use App\Http\Controllers\Controller;
use App\Http\Responses\Webmail\ComposeResponse;
use App\Http\Responses\Webmail\SendResponse;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Validator;

class Compose extends Controller {

    /**
     * The clientrepo repository instance.
     */
    protected $clientrepo;

    /**
     * The userrepo repository instance.
     */
    protected $userrepo;

    public function __construct(ClientRepository $clientrepo, UserRepository $userrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->clientrepo = $clientrepo;
        $this->userrepo = $userrepo;

    }

    /**
     * some notes
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function compose() {

        //defaults
        $recipients = [
            'users' => [],
            'specified' => false,
        ];

        //sending to a specified recipient(s) - [client]
        if (request('resource_type') == 'client' && is_numeric(request('resource_id'))) {
            $clients = $this->clientrepo->search(request('resource_id'));
            if ($client = $clients->first()) {
                if ($users = \App\Models\User::Where('clientid', request('resource_id'))->orderBy('account_owner', 'DESC')->get()) {
                    //create standard format list of users
                    foreach ($users as $user) {
                        $recipients['users'][] = [
                            'name' => $user->first_name . ' ' . $user->last_name,
                            'email' => $user->email,
                        ];
                    }
                    //update status
                    $recipients['specified'] = true;
                }
            }
        }

        //sending to a specified recipient(s) - [client]
        if (request('resource_type') == 'user' && is_numeric(request('resource_id'))) {
            if ($user = \App\Models\User::Where('id', request('resource_id'))->first()) {
                $recipients['users'][] = [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'email' => $user->email,
                ];
                //update status
                $recipients['specified'] = true;
            }
        }

        //get email templates
        $templates = \App\Models\WebmailTemplate::orderBy('webmail_template_name', 'ASC')->get();

        //which view
        $valid_views = [
            'modal',
            'page',
        ];
        if (request()->filled('view') && in_array(request('view'), $valid_views)) {
            $view = request('view');
        } else {
            $view = 'modal';
        }

        //reponse payload
        $payload = [
            'recipients' => $recipients,
            'view' => $view,
            'templates' => $templates,
        ];

        //show the form
        return new ComposeResponse($payload);

    }

    /**
     * process and send email
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send() {

        //[HEADER]
        //use Illuminate\Validation\Rule;
        //use Validator;
        //use App\Rules\NoTags;

        //custom error messages
        $messages = [
            'email_to.required' => __('lang.email_to') . ' - ' . __('lang.is_required'),
            'email_to.email' => __('lang.email_to') . ' - ' . __('lang.is_not_a_valid_email_address'),
            'email_subject.required' => __('lang.email_subject') . ' - ' . __('lang.is_required'),
            'email_body.required' => __('lang.email_body') . ' - ' . __('lang.is_required'),
            'email_from.required' => __('lang.email_from') . ' - ' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'email_to' => [
                'email',
                'required',
            ],
            'email_subject' => [
                'required',
            ],
            'email_body' => [
                'required',
            ],
            'email_from' => [
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

        //set from address
        if (request('email_from') == 'system_email') {
            $from_email = config('system.settings_email_from_address');
            $from_name = config('system.settings_email_from_name');
        } else {
            $from_email = auth()->user()->email;
            $from_name = auth()->user()->full_name;
        }

        /** ----------------------------------------------
         * queue the email in the database
         * ----------------------------------------------*/
        $email = new \App\Models\EmailQueue();
        $email->emailqueue_to = request('email_to');
        $email->emailqueue_subject = request('email_subject');
        $email->emailqueue_message = request('email_body');
        $email->emailqueue_from_email = $from_email;
        $email->emailqueue_from_name = $from_name;
        $email->emailqueue_type = 'direct';
        if (is_array(request('attachments'))) {
            $email->emailqueue_attachments = json_encode(request('attachments'));
        }
        $email->save();

        //show the form
        return new SendResponse();
    }

    /**
     * prefill text editor with the template
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function prefillTemplate() {

        if (!$template = \App\Models\WebmailTemplate::Where('webmail_template_id', request('id'))->first()) {
            abort(404);
        }

        //prefill editor with new data
        $jsondata['tinymce_new_data'][] = [
            'selector' => 'email_body', //no #
            'value' => $template->webmail_template_body,
        ];
        
        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);
    }

}