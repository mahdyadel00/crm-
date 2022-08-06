<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tickets\TicketStoreUpdate;
use App\Http\Responses\Tickets\CreateResponse;
use App\Http\Responses\Tickets\DestroyResponse;
use App\Http\Responses\Tickets\EditResponse;
use App\Http\Responses\Tickets\IndexResponse;
use App\Http\Responses\Tickets\ReplyResponse;
use App\Http\Responses\Tickets\ShowResponse;
use App\Http\Responses\Tickets\StoreReplyResponse;
use App\Http\Responses\Tickets\StoreResponse;
use App\Http\Responses\Tickets\UpdateResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\TicketReplyRepository;
use App\Repositories\TicketRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class Tickets extends Controller {

    /**
     * The ticket repository instance.
     */
    protected $ticketrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The attachment repository instance.
     */
    protected $attachmentrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    //contruct
    public function __construct(
        TicketRepository $ticketrepo,
        UserRepository $userrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        AttachmentRepository $attachmentrepo
    ) {

        //parent
        parent::__construct();

        $this->ticketrepo = $ticketrepo;
        $this->userrepo = $userrepo;
        $this->attachmentrepo = $attachmentrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;

        //authenticated
        $this->middleware('auth');

        $this->middleware('ticketsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'storeReply',
        ]);

        $this->middleware('ticketsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('ticketsMiddlewareEdit')->only([
            'edit',
            'update',
            'reply',
            'storeReply',
        ]);

        $this->middleware('ticketsMiddlewareReply')->only([
            'storeReply',
        ]);

        $this->middleware('ticketsMiddlewareShow')->only([
            'show',
        ]);

        $this->middleware('ticketsMiddlewareDownloadAttachment')->only([
            'downloadAttachment',
        ]);

        $this->middleware('ticketsMiddlewareDestroy')->only([
            'destroy',
        ]);

    }

    /**
     * Display a listing of tickets
     * @param object CategoryRepository instance of the repository
     * @return blade view | ajax view
     */
    public function index(CategoryRepository $categoryrepo) {

        //get tickets
        $tickets = $this->ticketrepo->search();

        //get all categories (type: ticket) - for filter panel
        $categories = $categoryrepo->get('ticket');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('tickets'),
            'tickets' => $tickets,
            'stats' => $this->statsWidget(),
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new ticket
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //get all categories (type: ticket) - for filter panel
        $categories = $categoryrepo->get('ticket');

        //if user is a client
        if (auth()->user()->is_client) {
            $clients_projects = \App\Models\Project::Where('project_clientid', auth()->user()->clientid)->get();
        } else {
            $clients_projects = [];
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
            'clients_projects' => $clients_projects,
        ];

        //show the view
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created ticket  in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(TicketStoreUpdate $request) {

        //create the item
        if (!$ticket_id = $this->ticketrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => request('ticket_clientid'),
                    'attachmentresource_type' => 'ticket',
                    'attachmentresource_id' => $ticket_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //get ticket
        $tickets = $this->ticketrepo->search($ticket_id);
        $ticket = $tickets->first();

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'ticket',
            'event_item_id' => $ticket->ticket_id,
            'event_item_lang' => 'event_opened_ticket',
            'event_item_content' => $ticket->ticket_subject,
            'event_item_content2' => '',
            'event_parent_type' => 'ticket',
            'event_parent_id' => $ticket->ticket_id,
            'event_parent_title' => $ticket->ticket_subject,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => $ticket->ticket_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $ticket->ticket_projectid,
            'event_notification_category' => 'notifications_tickets_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get team users
            if (auth()->user()->type == 'client') {
                $users = $this->userrepo->getTeamMembers('ids');
            }
            //get client users
            if (auth()->user()->type == 'team') {
                $users = $this->userrepo->getClientUsers($ticket->ticket_clientid, 'all', 'ids');
            }
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //the comment
            $data = [];
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\TicketCreated($user, $data, $ticket);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'ticket_id' => $ticket_id,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified ticket
     * @param object TicketReplyRepository instance of the repository
     * @param int $id ticket  id
     * @return \Illuminate\Http\Response
     */
    public function show(TicketReplyRepository $replyrepo, $id) {

        //get the ticket
        if (!$tickets = $this->ticketrepo->search($id)) {
            abort(409, __('lang.ticket_not_found'));
        }

        //ticket
        $ticket = $tickets->first();

        //get replies
        $replies = $replyrepo->search(['ticket_id' => $id]);

        //page settings
        $page = $this->pageSettings('ticket', $ticket);

        //mark all project events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'ticket')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //reponse payload
        $payload = [
            'page' => $page,
            'ticket' => $ticket,
            'replies' => $replies,
        ];

        //process reponse
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing the specified ticket
     * @param object CategoryRepository instance of the repository
     * @param int $id ticket id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the item
        $tickets = $this->ticketrepo->search($id);

        //client categories
        $categories = $categoryrepo->get('ticket');

        //not found
        if (!$ticket = $tickets->first()) {
            abort(409, __('lang.ticket_not_found'));
        }

        //projects
        $projects = \App\Models\Project::where('project_clientid', $ticket->ticket_clientid)->get();

        //reponse payload
        $payload = [
            'template' => 'add-edit-inc',
            'page' => $this->pageSettings('edit'),
            'ticket' => $ticket,
            'categories' => $categories,
            'projects' => $projects,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified ticket  in storage.
     * @param object TicketStoreUpdate instance of the request validation object
     * @param int $id ticket id
     * @return \Illuminate\Http\Response
     */
    public function update(TicketStoreUpdate $request, $id) {

        //custom error messages
        $messages = [];

        //get ticket
        $ticket = \App\Models\Ticket::Where('ticket_id', $id)->first();

        //old status
        $old_status = $ticket->ticket_status;

        //update
        if (!$this->ticketrepo->update($id)) {
            abort(409);
        }

        //get item
        $tickets = $this->ticketrepo->search($id);
        $ticket = $tickets->first();

        //reponse payload
        $payload = [
            'tickets' => $tickets,
            'ticket_id' => $id,
            'edit_source' => request('edit_source'),
        ];

        //if ticket was on hold and we are now opening it
        if ($ticket->ticket_status == 'on_hold' && in_array(request('ticket_status'), ['open', 'answered'])) {
            request()->merge([
                'switch_reply_option' => 'show_reply_button',
            ]);
        }

        //if ticket was on hold and we are now opening it
        if ($ticket->ticket_status != 'on_hold' && request('ticket_status') == 'on_hold') {
            request()->merge([
                'switch_reply_option' => 'hide_reply_button',
            ]);
        }

        //if ticket is now being closed
        if ($old_status != 'closed' && $ticket->ticket_status == 'closed') {

            /** ----------------------------------------------
             * record event [comment]
             * ----------------------------------------------*/
            $data = [
                'event_creatorid' => auth()->id(),
                'event_item' => 'ticket',
                'event_item_id' => $ticket->ticket_id,
                'event_item_lang' => 'event_closed_ticket',
                'event_item_content' => $ticket->ticket_subject,
                'event_item_content2' => '',
                'event_parent_type' => 'ticket',
                'event_parent_id' => $ticket->ticket_id,
                'event_parent_title' => $ticket->ticket_subject,
                'event_show_item' => 'yes',
                'event_show_in_timeline' => 'yes',
                'event_clientid' => $ticket->ticket_clientid,
                'eventresource_type' => 'project',
                'eventresource_id' => $ticket->ticket_projectid,
                'event_notification_category' => 'notifications_tickets_activity',
            ];
            //record event
            if ($event_id = $this->eventrepo->create($data)) {
                //get client users
                $users = $this->userrepo->getClientUsers($ticket->ticket_clientid, 'all', 'ids');
                //record notification
                $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
            }

            /** ----------------------------------------------
             * send email [comment
             * ----------------------------------------------*/
            if (isset($emailusers) && is_array($emailusers)) {
                //data
                $data = [
                    'by_first_name' => auth()->user()->first_name,
                    'by_last_name' => auth()->user()->last_name,
                ];
                //send to users
                if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                    foreach ($users as $user) {
                        $mail = new \App\Mail\TicketClosed($user, $data, $ticket);
                        $mail->build();
                    }
                }
            }

        }

        //generate a response
        return new UpdateResponse($payload);

    }

    /**
     * Show the form for posting a ticket reply.
     * @param int $id ticket  id
     * @return \Illuminate\Http\Response
     */
    public function reply($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the item
        $tickets = $this->ticketrepo->search($id);

        //not found
        if (!$ticket = $tickets->first()) {
            abort(409, __('lang.ticket_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'ticket' => $ticket,
        ];

        //response
        return new ReplyResponse($payload);
    }

    /**
     * Show the form for posting a ticket reply.
     * @param object TicketReplyRepository instance of the repository
     * @param int $id ticket  id
     * @return \Illuminate\Http\Response
     */
    public function storeReply(TicketReplyRepository $replyrepo) {

        //page settings
        $page = $this->pageSettings('');

        //get the item
        $tickets = $this->ticketrepo->search(request()->route('ticket'));
        $ticket = $tickets->first();

        //add missing data
        request()->merge([
            'ticketreply_clientid' => $ticket->ticket_clientid,
        ]);

        //custom error messages
        $messages = [
            'ticketreply_ticketid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'ticketreply_text' => 'required',
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

        //create the item
        if (!$reply_id = $replyrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get reply
        $reply = $replyrepo->search(['ticketreply_id' => $reply_id])->first();

        //update last updated
        $ticket->ticket_last_updated = NOW();
        $ticket->ticket_status = request('ticket_status'); //from middleware
        $ticket->save();

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => $reply->ticket->ticket_clientid,
                    'attachmentresource_type' => 'ticketreply',
                    'attachmentresource_id' => $reply_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //get refreshed reply (with attachments)
        $replies = $replyrepo->search(['ticketreply_id' => $reply_id]);
        $reply = $replies->first();

        //get refreshed ticket
        $tickets = $this->ticketrepo->search(request()->route('ticket'));
        $ticket = $tickets->first();

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'ticket',
            'event_item_id' => $ticket->ticket_id,
            'event_item_lang' => 'event_replied_ticket',
            'event_item_content' => request('ticketreply_text'),
            'event_item_content2' => $ticket->ticket_subject,
            'event_parent_type' => 'ticket',
            'event_parent_id' => $ticket->ticket_id,
            'event_parent_title' => $ticket->ticket_subject,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'event_clientid' => $ticket->ticket_clientid,
            'eventresource_type' => 'project',
            'eventresource_id' => $ticket->ticket_projectid,
            'event_notification_category' => 'notifications_tickets_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get team users
            if (auth()->user()->type == 'client') {
                $users = $this->userrepo->getTeamMembers('ids');
            }
            //get client users
            if (auth()->user()->type == 'team') {
                $users = $this->userrepo->getClientUsers($ticket->ticket_clientid, 'all', 'ids');
            }
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //the comment
            $data = $reply->toArray();
            //send to users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\TicketReply($user, $data, $ticket);
                    $mail->build();
                }
            }
        }

        //reponse payload
        $payload = [
            'replies' => $replies,
            'ticket' => $ticket,
        ];

        //response
        return new StoreReplyResponse($payload);

    }

    /**
     * Delete file attachments from the database and also delete the directory physically
     * @param object ticket instance of the ticket model object
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachments($ticket = '') {
        //loop through all the posted attachments for deletion
        if (is_array(request('attachments'))) {
            foreach (request('attachments') as $attachment_id => $value) {
                //only checked items
                if ($value == 'on' && is_numeric($attachment_id)) {
                    //get the attachment database object
                    if ($attachment = \App\Models\Attachment::find($attachment_id)) {
                        //make sure this attachments if for this ticket
                        if ($attachment->attachmentresource_id == $ticket->ticket_id && $attachment->attachmentresource_type == 'ticket') {
                            //sanity & delete
                            if ($attachment->attachment_directory != '') {
                                //delete directry
                                Storage::deleteDirectory("files/" . $attachment->attachment_directory);
                                //delete the dabase record
                                $attachment->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Remove the specified ticket  from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();

        foreach (request('ids') as $id => $value) {

            //only checked items
            if ($value == 'on') {
                //destroy the ticket
                $destroyrepo->destroyTicket($id);
                $allrows[] = $id;
            }
        }

        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //generate a response
        return new DestroyResponse($payload);
    }

    /**
     * download an attachment
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment() {

        //check if file exists in the database
        $attachment = \App\Models\Attachment::Where('attachment_uniqiueid', request()->route('uniqueid'))->first();

        //confirm thumb exists
        if ($attachment->attachment_filename != '') {
            $file_path = "files/$attachment->attachment_directory/$attachment->attachment_filename";
            if (Storage::exists($file_path)) {
                return Storage::download($file_path);
            }
        }
        abort(404, __('lang.file_not_found'));
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
            'crumbs' => [
                __('lang.tickets'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'tickets',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_tickets' => 'active',
            'sidepanel_id' => 'sidepanel-filter-tickets',
            'dynamic_search_url' => url('tickets/search?action=search&ticketresource_id=' . request('ticketresource_id') . '&ticketresource_type=' . request('ticketresource_type')),
            'load_more_button_route' => 'tickets',
            'source' => 'list',
            'crumbs_col_size' => 'col-lg-5',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_button_link_url' => url('tickets/create'),
        ];

        //tickets list page
        if ($section == 'tickets') {
            $page += [
                'meta_title' => __('lang.tickets'),
                'heading' => __('lang.tickets'),
                'mainmenu_tickets' => 'active',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //tickets list page
        if ($section == 'create') {
            $page['crumbs'] = [
                __('lang.tickets'),
                'compose new ticket',
            ];
            $page += [
                'meta_title' => __('lang.open_support_ticket'),
                'heading' => __('lang.tickets'),
                'mainmenu_tickets' => 'active',
            ];
            return $page;
        }

        //ticket page
        if ($section == 'ticket') {
            $page['crumbs'] = [
                __('lang.support_tickets'),
                __('lang.id') . ' #' . $data->ticket_id,
            ];
            $page['page'] = 'ticket';
            $page['heading'] = $data->ticket_subject;
            $page['crumbs_col_size'] = 'col-lg-9';
            return $page;
        }

        //return
        return $page;
    }

    /**
     * data for the stats widget
     * @return array
     */
    private function statsWidget($data = array()) {

        //get expense (all rows - for stats etc)
        $count_all = $this->ticketrepo->search('', ['stats' => 'count-all']);
        $count_open = $this->ticketrepo->search('', ['stats' => 'count-open']);
        $count_closed = $this->ticketrepo->search('', ['stats' => 'count-closed']);
        $count_answered = $this->ticketrepo->search('', ['stats' => 'count-answered']);

        //default values
        $stats = [
            [
                'value' => $count_all,
                'title' => __('lang.all'),
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => $count_open,
                'title' => __('lang.open'),
                'percentage' => '100%',
                'color' => 'bg-warning',
            ],
            [
                'value' => $count_answered,
                'title' => __('lang.answered'),
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
            [
                'value' => $count_closed,
                'title' => __('lang.closed'),
                'percentage' => '100%',
                'color' => 'bg-default',
            ],
        ];

        //return
        return $stats;
    }
}