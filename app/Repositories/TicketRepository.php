<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Ticket;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class TicketRepository {

    /**
     * The tickets repository instance.
     */
    protected $tickets;

    /**
     * Inject dependecies
     */
    public function __construct(Ticket $tickets) {
        $this->tickets = $tickets;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object ticket collection
     */
    public function search($id = '', $data = array()) {

        $tickets = $this->tickets->newQuery();

        // all client fields
        $tickets->selectRaw('*');

        //joins
        $tickets->leftJoin('clients', 'clients.client_id', '=', 'tickets.ticket_clientid');
        $tickets->leftJoin('users', 'users.id', '=', 'tickets.ticket_creatorid');
        $tickets->leftJoin('categories', 'categories.category_id', '=', 'tickets.ticket_categoryid');
        $tickets->leftJoin('projects', 'projects.project_id', '=', 'tickets.ticket_projectid');

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $tickets->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'tickets.ticket_id')
                    ->where('reminders.reminderresource_type', '=', 'ticket')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        //default where
        $tickets->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_ticket_id')) {
            $tickets->where('ticket_id', request('filter_ticket_id'));
        }
        if (is_numeric($id)) {
            $tickets->where('ticket_id', $id);
        }

        //filter: date (start)
        if (request()->filled('filter_ticket_created_start')) {
            $tickets->whereDate('ticket_created', '>=', request('filter_ticket_created_start'));
        }

        //filter: date (end)
        if (request()->filled('filter_ticket_created_end')) {
            $tickets->whereDate('ticket_created', '<=', request('filter_ticket_created_end'));
        }

        //filter clients
        if (request()->filled('filter_ticket_clientid')) {
            $tickets->where('ticket_clientid', request('filter_ticket_clientid'));
        }

        //stats: - counting
        if (isset($data['stats']) && $data['stats'] == 'count-open') {
            $tickets->where('ticket_status', 'open');
        }
        if (isset($data['stats']) && $data['stats'] == 'count-closed') {
            $tickets->where('ticket_status', 'closed');
        }
        if (isset($data['stats']) && $data['stats'] == 'count-answered') {
            $tickets->where('ticket_status', 'answered');
        }

        //resource filtering
        if (request()->filled('ticketresource_type') && request()->filled('ticketresource_id')) {
            switch (request('ticketresource_type')) {
            case 'client':
                $tickets->where('ticket_clientid', request('ticketresource_id'));
                break;
            case 'project':
                $tickets->where('ticket_projectid', request('ticketresource_id'));
                break;
            }
        }

        //filter status
        if (is_array(request('filter_ticket_status')) && !empty(array_filter(request('filter_ticket_status')))) {
            $tickets->whereIn('ticket_status', request('filter_ticket_status'));
        }

        //filter priority
        if (is_array(request('filter_ticket_priority')) && !empty(array_filter(request('filter_ticket_priority')))) {
            $tickets->whereIn('ticket_priority', request('filter_ticket_priority'));
        }

        //filter category
        if (is_array(request('filter_ticket_categoryid')) && !empty(array_filter(request('filter_ticket_categoryid')))) {
            $tickets->whereIn('ticket_categoryid', request('filter_ticket_categoryid'));
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $tickets->where(function ($query) {
                $query->Where('ticket_id', '=', request('search_query'));
                $query->orWhere('ticket_created', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');

                $query->orWhere('ticket_subject', 'LIKE', '%' . request('search_query') . '%');

                $query->orWhere('ticket_status', '=', request('search_query'));
                $query->orWhere('ticket_priority', '=', request('search_query'));

                $query->orWhereHas('category', function ($q) {
                    $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                });
                $query->orWhereHas('client', function ($q) {
                    $q->where('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                });
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('tickets', request('orderby'))) {
                $tickets->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $tickets->orderBy('client_company_name', request('sortorder'));
                break;
            case 'category':
                $tickets->orderBy('category_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $tickets->orderBy('ticket_id', 'desc');
        }

        //eager load
        $tickets->with([
            'attachments.creator',
        ]);

        //eager load counts
        $tickets->withCount([
            'attachments',
        ]);

        //stats - sum all
        if (isset($data['stats']) && in_array($data['stats'], [
            'count-all',
            'count-open',
            'count-closed',
            'count-answered',
        ])) {
            return $tickets->count();
        }

        // Get the results and return them.
        return $tickets->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $ticket = new $this->tickets;

        //data
        $ticket->ticket_categoryid = request('ticket_categoryid');
        $ticket->ticket_creatorid = auth()->id();
        $ticket->ticket_clientid = request('ticket_clientid');
        $ticket->ticket_projectid = request('ticket_projectid');
        $ticket->ticket_subject = request('ticket_subject');
        $ticket->ticket_message = request('ticket_message');
        $ticket->ticket_priority = request('ticket_priority');

        //save and return id
        if ($ticket->save()) {
            return $ticket->ticket_id;
        } else {
            return false;
        }
    }

    /**
     * update a record
     * @param int $id ticket id
     * @return bool
     */
    public function update($id) {

        //get the record
        if (!$ticket = $this->tickets->find($id)) {
            Log::error("record could not be found", ['process' => '[TicketRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'ticket_id' => $id ?? '']);
            return false;
        }

        //general
        $ticket->ticket_categoryid = request('ticket_categoryid');
        $ticket->ticket_projectid = request('ticket_projectid');
        $ticket->ticket_subject = request('ticket_subject');
        $ticket->ticket_message = request('ticket_message');
        $ticket->ticket_priority = request('ticket_priority');
        $ticket->ticket_status = request('ticket_status');

        //save
        if ($ticket->save()) {
            return $ticket->ticket_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[TicketRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}