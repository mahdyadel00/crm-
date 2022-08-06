<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for reminders
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Reminders\CloseCardResponse;
use App\Http\Responses\Reminders\CreateCardResponse;
use App\Http\Responses\Reminders\CreateResponse;
use App\Http\Responses\Reminders\DestroyCardResponse;
use App\Http\Responses\Reminders\DestroyResponse;
use App\Http\Responses\Reminders\EditCardResponse;
use App\Http\Responses\Reminders\EditResponse;
use App\Http\Responses\Reminders\ShowCardResponse;
use App\Http\Responses\Reminders\ShowResponse;
use App\Http\Responses\Reminders\ShowTopnavResponse;
use App\Http\Responses\Reminders\StoreCardResponse;
use App\Http\Responses\Reminders\StoreResponse;

class Reminders extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function show() {

        //validate
        if (request('resource_type') == '' || !is_numeric(request('resource_id'))) {
            abort(409);
        }

        //no reminder found
        if (!$reminder = \App\Models\Reminder::Where('reminderresource_type', request('resource_type'))
            ->where('reminderresource_id', request('resource_id'))
            ->where('reminder_userid', auth()->id())
            ->first()) {

            //for card -show the calender
            if (request('ref') == 'card') {
                return $this->create();
            } else {
                //for side panel - show create new splash
                $payload = [
                    'status' => 'none-found',
                    'resource_id' => request('resource_id'),
                    'resource_type' => request('resource_type'),
                    'reminder_ref' => request('reminder_ref'),
                    'reminder' => $reminder,
                ];
                return new ShowResponse($payload);
            }
        }

        //reponse payload
        $payload = [
            'status' => 'found',
            'has_reminder' => true,
            'reminder' => $reminder,
            'resource_id' => request('resource_id'),
            'resource_type' => request('resource_type'),
            'reminder_title' => $reminder->reminder_title,
            'reminder_notes' => $reminder->reminder_notes,
            'preset_date' => $reminder->reminder_datetime,
            'reminder_ajax_loading_target' => 'reminders-side-panel-body',
            'reminder_ajax_loading_target' => (request('ref') == 'card') ? 'card-reminders-container' : 'reminders-side-panel-body',
        ];

        //card
        if (request('ref') == 'card') {
            $payload += [
                'reminder_ajax_loading_target' => 'card-reminders-container',
            ];
            return new ShowCardResponse($payload);
        }

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing a new resource.
     * @return \Illuminate\Http\Response
     */
    public function edit() {

        //validate
        if (request('resource_type') == '' || !is_numeric(request('resource_id')) || !is_numeric(request('reminder_id'))) {
            abort(409);
        }

        //no reminder found show create form
        if (!$reminder = \App\Models\Reminder::Where('reminder_id', request('reminder_id'))
            ->Where('reminder_userid', auth()->id())->first()) {
            return $this->create();
        }

        //reponse payload
        $payload = [
            'status' => 'found',
            'has_reminder' => false,
            'show_delete_button' => true,
            'reminder' => $reminder,
            'resource_id' => request('resource_id'),
            'resource_type' => request('resource_type'),
            'reminder_title' => $reminder->reminder_title,
            'reminder_notes' => $reminder->reminder_notes,
            'preset_date' => $reminder->reminder_datetime,
            'reminder_ajax_loading_target' => 'reminders-side-panel-body',
            'reminder_ajax_loading_target' => (request('ref') == 'card') ? 'card-reminders-container' : 'reminders-side-panel-body',
        ];

        //card
        if (request('ref') == 'card') {
            $payload += [
                'reminder_ajax_loading_target' => 'card-reminders-container',
            ];
            return new EditCardResponse($payload);
        }

        //show the form
        return new EditResponse($payload);
    }

    /**
     * show calender
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //reponse payload
        $payload = [
            'show_delete_button' => false,
            'preset_date' => \Carbon\Carbon::now()->format('Y-m-d H:i'),
            'resource_id' => request('resource_id'),
            'resource_type' => request('resource_type'),
            'reminder_ajax_loading_target' => (request('ref') == 'card') ? 'card-reminders-container' : 'reminders-side-panel-body',
            'page' => $this->pageSettings('create'),
        ];

        //card
        if (request('ref') == 'card') {
            return new CreateCardResponse($payload);
        }

        //process reponse
        return new CreateResponse($payload);

    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //validate
        if (request('resource_type') == '' || !is_numeric(request('resource_id')) || request('reminder_datetime') == '') {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //validate for
        if (request('reminder_title') == '') {
            abort(409, __('lang.reminder_title') . ' - ' . __('lang.is_required'));
        }

        //cannot be in the past if (Carbon::now()->gt(Carbon::parse($date))
        if (\Carbon\Carbon::now()->gt(\Carbon\Carbon::parse(request('reminder_datetime')))) {
            abort(409, __('lang.reminder_cannot_be_past'));
        }

        //get the linked item
        if (!$meta_title = $this->getResourceItem(request('resource_type'), request('resource_id'))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //delete any existing reminders for this user
        \App\Models\Reminder::Where('reminderresource_type', request('resource_type'))
            ->where('reminderresource_id', request('resource_id'))
            ->where('reminder_userid', auth()->id())
            ->delete();

        //save the reminder
        $reminder = new \App\Models\Reminder();
        $reminder->reminder_userid = auth()->id();
        $reminder->reminder_datetime = request('reminder_datetime');
        $reminder->reminder_timestamp = now();
        $reminder->reminder_title = request('reminder_title');
        $reminder->reminder_meta = $meta_title;
        $reminder->reminderresource_type = request('resource_type');
        $reminder->reminderresource_id = request('resource_id');
        $reminder->reminder_notes = request('reminder_notes');
        $reminder->reminder_status = 'active';
        $reminder->save();

        //reponse payload
        $payload = [
            'reminder' => $reminder,
            'has_reminder' => true,
            'resource_type' => request('resource_type'),
            'resource_id' => request('resource_id'),
        ];

        //card
        if (request('ref') == 'card') {
            return new StoreCardResponse($payload);
        }

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * get the linked resource item
     *
     * @param  string $resource_type e.g. lead, task, etc
     * @param int $resource_id id of the resource
     * @return string
     */
    public function getResourceItem($resource_type = '', $resource_id = '') {

        //validate
        if (!is_numeric($resource_id) || $resource_type == '') {
            return false;
        }

        //client
        if ($resource_type == 'client') {
            if (!$client = \App\Models\Client::Where('client_id', $resource_id)->first()) {
                return false;
            }
            return $client->client_company_name;
        }

        //project
        if ($resource_type == 'project') {
            if (!$project = \App\Models\Project::Where('project_id', $resource_id)->first()) {
                return false;
            }
            return $project->project_title;
        }

        //invoice
        if ($resource_type == 'invoice') {
            if (!$invoice = \App\Models\Invoice::Where('bill_invoiceid', $resource_id)->first()) {
                return false;
            }
            return $invoice->formatted_bill_invoiceid;
        }

        //estimate
        if ($resource_type == 'estimate') {
            if (!$estimate = \App\Models\Estimate::Where('bill_estimateid', $resource_id)->first()) {
                return false;
            }
            return $estimate->formatted_bill_estimateid;
        }

        //task
        if ($resource_type == 'task') {
            if (!$task = \App\Models\Task::Where('task_id', $resource_id)->first()) {
                return false;
            }
            return $task->task_title;
        }

        //lead
        if ($resource_type == 'lead') {
            if (!$lead = \App\Models\Lead::Where('lead_id', $resource_id)->first()) {
                return false;
            }
            return $lead->lead_title;
        }

        //ticket
        if ($resource_type == 'ticket') {
            if (!$ticket = \App\Models\Ticket::Where('ticket_id', $resource_id)->first()) {
                return false;
            }
            return $ticket->ticket_subject;
        }

        //proposal
        if ($resource_type == 'proposal') {
            if (!$doc = \App\Models\Proposal::Where('doc_id', $resource_id)->first()) {
                return false;
            }
            return $doc->doc_title;
        }

        //contract
        if ($resource_type == 'contract') {
            if (!$doc = \App\Models\Contract::Where('doc_id', $resource_id)->first()) {
                return false;
            }
            return $doc->doc_title;
        }

        //show the form
        return new ShowResponse($payload);

    }

    /**
     * Delete a resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function delete() {

        //validate id
        if (!request()->filled('reminder_id')) {
            abort(409);
        }

        //delete any existing reminders for this user
        \App\Models\Reminder::Where('reminder_id', request('reminder_id'))
            ->where('reminder_userid', auth()->id())
            ->delete();

        //reponse payload
        $payload = [
            'has_reminder' => false,
            'resource_id' => request('resource_id'),
            'resource_type' => request('resource_type'),
        ];

        //card
        if (request('ref') == 'card') {
            return new DestroyCardResponse($payload);
        }

        //process reponse
        return new DestroyResponse($payload);

    }

    /**
     * Delete a resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function close() {

        if (request()->filled('resource_id') && request()->filled('resource_type')) {
            if (\App\Models\Reminder::Where('reminderresource_type', request('resource_type'))
                ->where('reminderresource_id', request('resource_id'))
                ->where('reminder_userid', auth()->id())
                ->first()) {
                return $this->show();
            }
        }

        //reponse payload
        $payload = [
            'has_reminder' => false,
            'resource_id' => request('resource_id'),
            'resource_type' => request('resource_type'),
        ];

        return new CloseCardResponse($payload);

    }

    /**
     * get reminders to show in the topnav
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function topNavFeed() {

        $reminders = \App\Models\Reminder::Where('reminder_userid', auth()->id())
            ->where('reminder_status', 'due')
            ->orderBy('reminder_datetime', 'DESC')->get();

        //reponse payload
        $payload = [
            'reminders' => $reminders,
        ];

        //show the form
        return new ShowTopnavResponse($payload);

    }

    /**
     * delete a single reminder (usually from topnav checkbox)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteReminder($id) {

        //delete record
        if (is_numeric($id)) {
            \App\Models\Reminder::Where('reminder_id', $id)
                ->where('reminder_userid', auth()->id())->delete();
        }

        //count reminders

    }

    /**
     * delete all reminders (usually from topnav checkbox)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAllReminders() {

        //delete record
        \App\Models\Reminder::Where('reminder_userid', auth()->id())->where('reminder_status', 'due')->delete();

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

        ];

        //return
        return $page;
    }
}