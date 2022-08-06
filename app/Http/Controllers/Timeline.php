<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for timeline
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Timeline\IndexResponse;
use App\Permissions\ProjectPermissions;
use App\Repositories\ClientRepository;
use App\Repositories\EventRepository;
use App\Repositories\ProjectRepository;

class Timeline extends Controller {

    /**
     * The events repository instance.
     */
    protected $eventrepo;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The client permission instance.
     */
    protected $clientrepo;

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    public function __construct(
        EventRepository $eventrepo,
        ProjectPermissions $projectpermissions,
        ClientRepository $clientrepo,
        ProjectRepository $projectrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->eventrepo = $eventrepo;
        $this->projectpermissions = $projectpermissions;
        $this->clientrepo = $clientrepo;
        $this->projectrepo = $projectrepo;

    }

    /**
     * show a list of timeline events
     * @return \Illuminate\Http\Response
     */
    public function projectTimeline() {

        //basic settings
        $page = [];

        //filter
        request()->merge([
            'eventresource_type' => request('timelineresource_type'),
            'eventresource_id' => request('timelineresource_id'),
        ]);

        //get events
        $events = $this->eventrepo->search([
            'pagination' => config('settings.limits.pagination_project_timeline'),
            'filter' => 'timeline_visible',
        ]);

        //process events
        $events = $this->processEvents($events);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'events' => $events,
            'count' => $events->total(),
            'replace_actions_nav' => '',
        ];

        //response
        return new IndexResponse($payload);
    }

    /**
     * show a list of timeline events
     * @return \Illuminate\Http\Response
     */
    public function clientTimeline() {

        //basic settings
        $page = [];

        //filter
        request()->merge([
            'eventresource_type' => request('timelineresource_type'),
            'eventresource_id' => request('timelineresource_id'),
        ]);

        //get events
        $events = $this->eventrepo->search([
            'pagination' => config('settings.limits.pagination_project_timeline'),
            'filter' => 'timeline_visible',
        ]);

        //process events
        $events = $this->processEvents($events);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'events' => $events,
            'count' => $events->total(),
            'replace_actions_nav' => '',
        ];

        //get clent resource
        if (request('request_source') == 'client' && request()->filled('timelineclient_id')) {

            //get the client (full format)
            $clients = $this->clientrepo->search(request('timelineclient_id'));
            if ($client = $clients->first()) {
                $payload['replace_actions_nav'] = 'client';
                $payload['client'] = $client;
            }
        }

        //response
        return new IndexResponse($payload);
    }

    /**
     * [sanity]
     *  Additional processing of the time line as follows:
     *       - Do not show clients [task] related events, if the project settings do now allos
     *
     * @return object
     */
    private function processEvents($events = '') {

        if ($events instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            foreach ($events as $key => $event) {

                //hide [task events] for clients as per project settings
                if ($event->event_parent_type == 'task' && auth()->user()->is_client) {
                    if (!$this->projectpermissions->check('tasks-view', $event->eventresource_id)) {
                        $events->forget($key);
                    }
                }

                //hide event as per user role [tickets]
                if ($event->event_item == 'ticket' && auth()->user()->role->role_tickets == 0) {
                    $events->forget($key);
                }

                //hide event as per user role [invoices]
                if ($event->event_item == 'invoice' && auth()->user()->role->role_invoices == 0) {
                    $events->forget($key);
                }

                //hide event as per user role [payments]
                if ($event->event_item == 'payment' && auth()->user()->role->role_invoices == 0) {
                    $events->forget($key);
                }

            }
            return $events;
        }
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
