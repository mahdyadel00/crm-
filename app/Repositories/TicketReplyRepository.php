<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for ticket replies
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\TicketReply;
use Illuminate\Http\Request;
use Log;

class TicketReplyRepository {

    /**
     * The replies repository instance.
     */
    protected $replies;

    /**
     * Inject dependecies
     */
    public function __construct(TicketReply $replies) {
        $this->replies = $replies;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object ticket collection
     */
    public function search($data = array()) {

        $replies = $this->replies->newQuery();

        // all client fields
        $replies->selectRaw('*');

        //joins
        $replies->leftJoin('users', 'users.id', '=', 'ticket_replies.ticketreply_creatorid');

        //default where
        $replies->whereRaw("1 = 1");

        if (isset($data['ticket_id'])) {
            $replies->where('ticketreply_ticketid', $data['ticket_id']);
        }

        if (isset($data['ticketreply_ticketid'])) {
            $replies->where('ticketreply_ticketid', $data['ticketreply_ticketid']);
        }

        if (isset($data['ticketreply_id'])) {
            $replies->where('ticketreply_id', $data['ticketreply_id']);
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('ticket_replies', request('orderby'))) {
                $replies->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            }
        } else {
            //default sorting
            $replies->orderBy('ticketreply_id', 'asc');
        }

        //eager load
        $replies->with([
            'attachments.creator',
            'ticket',
        ]);

        //eager load counts
        $replies->withCount([
            'attachments',
        ]);

        // Get the results and return them.
        return $replies->get();
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $reply = new $this->replies;

        //data
        $reply->ticketreply_ticketid = request('ticketreply_ticketid');
        $reply->ticketreply_creatorid = auth()->id();
        $reply->ticketreply_clientid = request('ticketreply_clientid');
        $reply->ticketreply_text = request('ticketreply_text');

        //save and return id
        if ($reply->save()) {
            return $reply->ticketreply_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[TicketRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}