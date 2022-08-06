<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for events
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Event;
use App\Models\EventTracking;
use Illuminate\Http\Request;
use Log;

class EventRepository {

    /**
     * The events repository instance.
     */
    protected $events;

    /**
     * The tracking repository instance.
     */
    protected $tracking;

    /**
     * Inject dependecies
     */
    public function __construct(Event $events, EventTracking $tracking) {
        $this->events = $events;
        $this->tracking = $tracking;
    }

    /**
     * Search model
     * @param array $data information payload
     * @return object events collection
     */
    public function search($data = []) {

        $events = $this->events->newQuery();

        //joins
        $events->leftJoin('users', 'users.id', '=', 'events.event_creatorid');

        // all client fields
        $events->selectRaw('*');

        //default where
        $events->whereRaw("1 = 1");

        //filter by eventresource_type
        if (request()->filled('eventresource_type')) {
            $events->where('eventresource_type', request('eventresource_type'));
        }

        //filter by eventresource_id
        if (request()->filled('eventresource_id')) {
            $events->where('eventresource_id', request('eventresource_id'));
        }

        //filter by client (for client timelone)
        if (request()->filled('timelineclient_id')) {
            $events->where('event_clientid', request('timelineclient_id'));
        }

        //filter only visible items
        if(isset($data['filter']) && $data['filter'] == 'timeline_visible'){
            $events->where('event_show_in_timeline', 'yes');
        }

        //default sorting
        $events->orderBy('event_id', 'desc');

        //was pagination passed
        if (isset($data['pagination']) && is_numeric($data['pagination'])) {
            $pagination = $data['pagination'];
        } else {
            $pagination = config('system.settings_system_pagination_limits');
        }

        // Get the results and return them.
        return $events->paginate($pagination);
    }


    

    /**
     * Create a new record
     * @param array $data payload data
     * @param array $user user information
     * @return mixed object|bool
     */
    public function create($data = [], $users = []) {

        //save new user
        $event = new $this->events;

        //data
        $event->event_creatorid = $data['event_creatorid'];
        $event->event_clientid = $data['event_clientid'];
        $event->event_creator_name = (isset($data['event_creator_name'])) ? $data['event_creator_name'] : '';
        $event->event_item = $data['event_item'];
        $event->event_item_id = $data['event_item_id'];
        $event->event_item_content = $data['event_item_content'] ?? '';
        $event->event_item_content2 = $data['event_item_content2'] ?? '';
        $event->event_item_content3 = $data['event_item_content3'] ?? '';
        $event->event_item_content4 = $data['event_item_content4'] ?? '';
        $event->event_item_lang = $data['event_item_lang'];
        $event->event_item_lang_alt = $data['event_item_lang_alt'] ?? '';
        $event->event_parent_type = $data['event_parent_type'];
        $event->event_parent_id = $data['event_parent_id'];
        $event->event_parent_title = $data['event_parent_title'];
        $event->event_show_item = $data['event_show_item'];
        $event->event_show_in_timeline = $data['event_show_in_timeline'];
        $event->eventresource_type = $data['eventresource_type'];
        $event->eventresource_id = $data['eventresource_id'];
        $event->event_notification_category = $data['event_notification_category'];
        //save and return id
        if ($event->save()) {
            return $event->event_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EventRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}