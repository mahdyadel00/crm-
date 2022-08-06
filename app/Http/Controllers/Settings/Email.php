<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for email settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Email\IndexResponse;
use App\Http\Responses\Settings\Email\LogResponse;
use App\Http\Responses\Settings\Email\QueueResponse;
use App\Http\Responses\Settings\Email\TestEmailResponse;
use App\Http\Responses\Settings\Email\UpdateResponse;
use App\Repositories\SettingsRepository;
use Illuminate\Http\Request;
use Validator;

class Email extends Controller {

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
    public function general() {

        //crumbs, page data & stats
        $page = $this->pageSettings('general');

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'section' => 'general',
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
    public function updateGeneral() {

        //validate the form
        $validator = Validator::make(request()->all(), [
            'settings_email_from_address' => 'required|email',
            'settings_email_from_name' => 'required',
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->settingsrepo->updateEmailGeneral()) {
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
    public function smtp() {

        //crumbs, page data & stats
        $page = $this->pageSettings('smtp');

        $settings = \App\Models\Settings::find(1);

        //reponse payload
        $payload = [
            'page' => $page,
            'section' => 'smtp',
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
    public function updateSMTP() {

        //validate the form
        $validator = Validator::make(request()->all(), [
            'settings_email_smtp_host' => 'required_with:settings_email_smtp_status,on',
            'settings_email_smtp_port' => 'required_with:settings_email_smtp_status,on',
            'settings_email_smtp_username' => 'required_with:settings_email_smtp_status,on',
            'settings_email_smtp_password' => 'required_with:settings_email_smtp_status,on',
        ]);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->settingsrepo->updateEmailSMTP()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * show form for send a test email
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmail() {

        //default what to show in modal
        $show = 'form';

        //get settings
        $settings = \App\Models\Settings::find(1);

        //check if cronjon has run
        if ($settings->settings_cronjob_has_run != 'yes') {
            $show = 'error';
        }

        //reponse payload
        $payload = [
            'section' => 'form',
            'show' => $show,
        ];

        //show the view
        return new TestEmailResponse($payload);
    }

    /**
     * Send a test email
     *
     * @return \Illuminate\Http\Response
     */
    public function testEmailAction() {

        //validate
        if (!request()->filled('email')) {
            abort(409, __('lang.fill_in_all_required_fields'));
        }

        /** ----------------------------------------------
         * send a test email
         * ----------------------------------------------*/
        $data = [
            'notification_subject' => __('lang.email_delivery_test'),
            'notification_title' => __('lang.email_delivery_test'),
            'notification_message' => __('lang.email_delivery_this_is_a_test'),
            'first_name' => auth()->user()->first_name,
            'email' => request('email'),
        ];
        $mail = new \App\Mail\TestEmail($data);
        $mail->build();

        //reponse payload
        $payload = [
            'section' => 'success',
        ];

        //show the view
        return new TestEmailResponse($payload);
    }

    /**
     * run tests to check if SMTP ports are open
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function testSMTP() {

        $host = 'smtp.mailtrap.io';
        $ports = array(25, 2525, 465, 2525);

        //check if fsockopen() is enabled
        if (!function_exists('fsockopen')) {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-error-fsockopen',
                'action' => 'show',
            ];
            return response()->json($jsondata);
        }

        $results = '';
        $error_count = 0;
        foreach ($ports as $port) {
            //$connection = @fsockopen($host, $port);
            $connection = @fsockopen($host, $port, $errno, $errstr, 15);

            if (is_resource($connection)) {
                $results .= '<tr><td class="p-l-15">' . __('lang.smtp_port') . ' (' . $port . ')</td><td class="text-center">
                    <div class="inline-block label label-rounded label-success">' . __('lang.open') . '</div></td></tr>';
                fclose($connection);
            } else {
                $results .= '<tr><td class="p-l-15">' . __('lang.smtp_port') . ' (' . $port . ')</td><td class="text-center">
                    <div class="inline-block label label-rounded label-danger">' . __('lang.closed') . '</div></td></tr>';
                $error_count++;
            }
        }

        //errors
        if ($error_count == 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-smtp-passed',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#email-testing-tool-smtp-results-passed',
                'action' => 'replace',
                'value' => $results,
            ];
            return response()->json($jsondata);
        } else {
            $jsondata['dom_visibility'][] = [
                'selector' => '.email-testing-tool-sections',
                'action' => 'hide',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#email-testing-tool-smtp-failed',
                'action' => 'show',
            ];
            $jsondata['dom_html'][] = [
                'selector' => '#email-testing-tool-smtp-results-failed',
                'action' => 'replace',
                'value' => $results,
            ];
            return response()->json($jsondata);
        }

    }

    /**
     * Show email queue
     * @return blade view | ajax view
     */
    public function queueShow() {

        //get all emails
        $emails = \App\Models\EmailQueue::query()
            ->orderBy('emailqueue_id', 'DESC')
            ->paginate(config('system.settings_system_pagination_limits'));

        //payload
        $payload = [
            'page' => $this->pageSettings('queue'),
            'emails' => $emails,
        ];

        //show the view
        return new QueueResponse($payload);
    }

    /**
     * Show the email
     * @return blade view | ajax view
     */
    public function queueRead($id) {

        if (!$email = \App\Models\EmailQueue::Where('emailqueue_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('pages/settings/sections/email/queue/read', compact('email'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //fix emailfooer
        $jsondata['dom_classes'][] = [
            'selector' => 'style',
            'action' => 'remove',
            'value' => 'footer',
        ];

        //remove <style> tags
        $jsondata['dom_visibility'][] = [
            'selector' => '.settings-email-view-wrapper > style',
            'action' => 'hide-remove',
        ];

        //render
        return response()->json($jsondata);
    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function queueDelete($id) {

        if (!$email = \App\Models\EmailQueue::Where('emailqueue_id', $id)->first()) {
            abort(404);
        }

        //delete record
        $email->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#email_' . $id,
            'action' => 'slideup-slow-remove',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * delete all emails in the queue
     *
     * @return \Illuminate\Http\Response
     */
    public function queuePurge() {

        //delete all rows
        \App\Models\EmailQueue::getQuery()->delete();

        //remove all rows
        $jsondata['dom_visibility'][] = array(
            'selector' => '.settings-each-email',
            'action' => 'hide',
        );
        $jsondata['dom_visibility'][] = array(
            'selector' => '.loadmore-button-container',
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * queue all emails again
     *
     * @return \Illuminate\Http\Response
     */
    public function queueReschedule() {

        //delete all rows
        \App\Models\EmailQueue::where('emailqueue_status', 'processing')
            ->update(['emailqueue_status' => 'new']);

        $jsondata['dom_html'][] = [
            'selector' => '.emails_col_emailqueue_status',
            'action' => 'replace',
            'value' => __('lang.new'),
        ];

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * Show email log
     * @return blade view | ajax view
     */
    public function logShow() {

        //get all emails
        $emails = \App\Models\EmailLog::query()
            ->orderBy('emaillog_id', 'DESC')
            ->paginate(config('system.settings_system_pagination_limits'));

        //payload
        $payload = [
            'page' => $this->pageSettings('log'),
            'emails' => $emails,
        ];

        //show the view
        return new LogResponse($payload);
    }

    /**
     * Show the email
     * @return blade view | ajax view
     */
    public function logRead($id) {

        if (!$email = \App\Models\EmailLog::Where('emaillog_id', $id)->first()) {
            abort(404);
        }

        //page
        $html = view('pages/settings/sections/email/log/read', compact('email'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //fix emailfooer
        $jsondata['dom_classes'][] = [
            'selector' => 'style',
            'action' => 'remove',
            'value' => 'footer',
        ];

        //remove <style> tags
        $jsondata['dom_visibility'][] = [
            'selector' => '.settings-email-view-wrapper > style',
            'action' => 'hide-remove',
        ];

        //render
        return response()->json($jsondata);
    }

    /**
     * delete a record
     *
     * @return \Illuminate\Http\Response
     */
    public function logDelete($id) {

        if (!$email = \App\Models\EmailLog::Where('emaillog_id', $id)->first()) {
            abort(404);
        }

        //delete record
        $email->delete();

        //remove table row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#email_' . $id,
            'action' => 'slideup-slow-remove',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * delete all emails in the log
     *
     * @return \Illuminate\Http\Response
     */
    public function logPurge() {

        //delete all rows
        \App\Models\EmailLog::getQuery()->delete();

        //remove all rows
        $jsondata['dom_visibility'][] = array(
            'selector' => '.settings-each-email',
            'action' => 'hide',
        );
        $jsondata['dom_visibility'][] = array(
            'selector' => '.loadmore-button-container',
            'action' => 'hide',
        );

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        //general settings
        if ($section == 'general') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.email'),
                __('lang.general_settings'),
            ];
        }

        //smtp settings
        if ($section == 'smtp') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.email'),
                __('lang.smtp_settings'),
            ];
        }

        //smtp settings
        if ($section == 'queue') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.email'),
                __('lang.email_queue'),
            ];
        }

        //smtp settings
        if ($section == 'log') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.email'),
                __('lang.email_log'),
            ];
        }

        return $page;
    }

}
