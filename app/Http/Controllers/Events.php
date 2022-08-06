<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for events
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Common\CommonResponse;
use App\Http\Responses\Events\TopNavResponse;
use App\Repositories\EventTrackingRepository;

class Events extends Controller {

    /**
     * The events tracking repository instance.
     */
    protected $trackingrepo;

    public function __construct(EventTrackingRepository $trackingrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->trackingrepo = $trackingrepo;
    }

    /**
     * Display events on top nav dropdown
     * @return \Illuminate\Http\Response
     */
    public function topNavEvents() {

        //filter
        request()->merge([
            'eventtracking_userid' => auth()->id(),
        ]);

        //get team members
        $pagination = config('system.settings_system_pagination_limits');
        $events = $this->trackingrepo->search($pagination);

        //count unread
        $unread = \App\Models\EventTracking::where('eventtracking_userid', auth()->id())
        ->where('eventtracking_status', 'unread')
        ->count();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'events' => $events,
            'count' => $unread,
        ];

        //show the view
        return new TopNavResponse($payload);
    }

    /**
     * mark users events as read
     * @param int $id event id
     * @return \Illuminate\Http\Response
     */
    public function markMyEventRead($id) {

        //validate and process
        if ($event = \App\Models\EventTracking::Where('eventtracking_eventid', $id)
            ->Where('eventtracking_userid', auth()->id())
            ->first()) {

            //mark as read
            $event->eventtracking_status = 'read';
            $event->save();

            //reponse payload
            $payload = [
                'type' => 'remove-my-event',
                'element' => "#event_" . $event->eventtracking_eventid,
                'count' => auth()->user()->count_unread_notifications,
            ];

            //hide element
            return new CommonResponse($payload);
        }
    }

    /**
     * mark all users events as read
     * @return \Illuminate\Http\Response
     */
    public function markAllMyEventRead() {

        //update all
        \App\Models\EventTracking::where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);
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