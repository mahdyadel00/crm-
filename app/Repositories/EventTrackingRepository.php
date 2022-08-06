<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for event tracking
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\EventTracking;
use Illuminate\Http\Request;
use Log;

class EventTrackingRepository {

    /**
     * The tracking repository instance.
     */
    protected $eventtracking;

    /**
     * Inject dependecies
     */
    public function __construct(EventTracking $eventtracking) {
        $this->eventtracking = $eventtracking;
    }

    /**
     * Search model
     * @param int $pagination optional pagination limit
     * @return object attachments collection
     */
    public function search($pagination = 0) {

        $eventtracking = $this->eventtracking->newQuery();

        //joins
        $eventtracking->leftJoin('events', 'events.event_id', '=', 'events_tracking.eventtracking_eventid');
        $eventtracking->leftJoin('users', 'users.id', '=', 'events.event_creatorid');

        // all client fields
        $eventtracking->selectRaw('*');

        //default where
        $eventtracking->whereRaw("1 = 1");

        //filter by user
        if (request()->filled('eventtracking_userid')) {
            $eventtracking->where('eventtracking_userid', request('eventtracking_userid'));
        }

        //filter by status
        if (request()->filled('eventtracking_status')) {
            $eventtracking->where('eventtracking_status', request('eventtracking_status'));
        }

        //default sorting
        $eventtracking->orderBy('eventtracking_id', 'desc');

        //was pagination passed
        if (!is_numeric($pagination)) {
            $pagination = config('system.settings_system_pagination_limits');
        }

        // Get the results and return them.
        return $eventtracking->paginate($pagination);
    }

    /**
     * count number of unread evenets
     * @return int
     */
    public function countUnread() {

        $eventtracking = $this->eventtracking->newQuery();

        // all client fields
        $eventtracking->selectRaw('*');

        //default where
        $eventtracking->where('eventtracking_status', 'unread');
        $eventtracking->where('eventtracking_userid', auth()->id());
        return $eventtracking->count();
    }

    /**
     * Record users events
     * @string $event type of event
     * $mixed $users can be a user object or an array of users id's
     * @param string $event event information
     * @param object $users instance of the users model object
     * @param int $event_id event resource id
     * @return bool
     */
    function recordEvent($event = '', $users = '', $event_id = '') {

        //list of events recorded - will be returned for use in emailer
        $list = [];

        //validate
        if (!is_array($event) || !is_numeric($event_id)) {
            Log::error("validation error - invalid params", ['process' => '[EventTrackingRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //we passed an object
        if ($users instanceof \Illuminate\Database\Eloquent\Collection) {
            $userlist = [];
            foreach ($users as $user) {
                $userlist[] = $user->id;
            }
            $users = $userlist;
        }

        //we passed a sigle user object
        if ($users instanceof \App\Models\User) {
            $userlist[] = $users->id;
            $users = $userlist;
        }

        //validate users array
        if (!is_array($users)) {
            return false;
        }

        //add to users notifiable events
        foreach ($users as $userid) {
            //skip for current user
            if ($userid != auth()->id() && $event['event_notification_category'] != '') {
                //only record notifiable events here
                if ($user = \App\Models\User::Where('id', $userid)->first()) {

                    //get users role
                    $role = \App\Models\Role::Where('role_id', $user->role_id)->first();

                    //default
                    $notification = 'no';

                    //[everyone] - notifications_tickets_activity
                    if ($event['event_notification_category'] == 'notifications_tickets_activity') {
                        $notification = $user->notifications_tickets_activity;
                    }

                    //[everyone] - notifications_projects_activity
                    if ($event['event_notification_category'] == 'notifications_projects_activity') {
                        $notification = $user->notifications_projects_activity;
                        //for clients - exclude 'client invisible items' (e.g. files)
                        if ($user->type == 'client') {
                            if (isset($event['event_client_visibility']) && $event['event_client_visibility'] == 'no') {
                                $notification = 'no';
                            }
                        }
                    }

                    //[team] notifications_tasks_activity
                    if ($event['event_notification_category'] == 'notifications_tasks_activity') {
                        $notification = $user->notifications_tasks_activity;
                    }

                    //[team] notifications_new_assignement
                    if ($event['event_notification_category'] == 'notifications_new_assignement' && $user->type == 'team') {
                        $notification = $user->notifications_new_assignement;
                    }

                    //[team] notifications_new_payment
                    if ($event['event_notification_category'] == 'notifications_new_payment' && $user->type == 'team') {
                        $notification = $user->notifications_new_payment;
                    }

                    //[team] notifications_leads_activity
                    if ($event['event_notification_category'] == 'notifications_leads_activity' && $user->type == 'team') {
                        $notification = $user->notifications_leads_activity;
                    }

                    //[client] notifications_billing_activity
                    if ($event['event_notification_category'] == 'notifications_billing_activity') {
                        $notification = $user->notifications_billing_activity;
                    }

                    //[client] notifications_projects_activity
                    if ($event['event_notification_category'] == 'notifications_projects_activity' && $user->type == 'client') {
                        $notification = $user->notifications_projects_activity;
                    }

                    //[tickets] - only support ticket members
                    if ($event['event_notification_category'] == 'notifications_tickets_activity') {
                        if ($user->type == 'team') {
                            if ($role->role_tickets == 0) {
                                $notification = 'no';
                            }
                        }
                    }

                    //[proposals] - only support ticket members
                    if ($event['event_notification_category'] == 'notifications_tickets_activity') {
                        if ($user->type == 'team') {
                            if ($role->role_tickets == 0) {
                                $notification = 'no';
                            }
                        }
                    }

                    //record the event
                    if ($notification == 'yes' || $notification == 'yes_email') {
                        $eventtracking = new $this->eventtracking;
                        $eventtracking->eventtracking_eventid = $event_id;
                        $eventtracking->eventtracking_userid = $userid;
                        $eventtracking->eventtracking_source = $event['event_item'];
                        $eventtracking->eventtracking_source_id = $event['event_item_id'];
                        $eventtracking->parent_type = $event['event_parent_type'];
                        $eventtracking->parent_id = $event['event_parent_id'];
                        $eventtracking->resource_type = $event['eventresource_type'];
                        $eventtracking->resource_id = $event['eventresource_id'];
                        $eventtracking->save();
                    }

                    //save to array that will be used to send email
                    if ($notification == 'yes_email') {
                        $list[] = $userid;
                    }
                }
            }
        }
        //return list of users that were send the notification
        return $list;
    }

}