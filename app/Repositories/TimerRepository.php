<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for timers
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Timer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;
use DB;


class TimerRepository {

    /**
     * The leads repository instance.
     */
    protected $timer;

    /**
     * Inject dependecies
     */
    public function __construct(Timer $timer) {
        $this->timer = $timer;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object timer collection
     */
    public function search($id = '') {

        $timers = $this->timer->newQuery();

        //joins
        $timers->leftJoin('users', 'users.id', '=', 'timers.timer_creatorid');
        $timers->leftJoin('tasks', 'tasks.task_id', '=', 'timers.timer_taskid');
        $timers->leftJoin('projects', 'projects.project_id', '=', 'timers.timer_projectid');

        // all client fields
        $timers->selectRaw('*');

        //sum all of a tasks total timers
        if (request()->filled('filter_grouping') && in_array(request('filter_grouping'), ['task', 'user'])) {
            $timers->selectRaw('(SELECT sum(timer_time))
                                      AS time');
        } else {
            $timers->selectRaw('(SELECT timer_time) AS time');
        }

        //default where
        $timers->whereRaw("1 = 1");

        if (is_numeric($id)) {
            $timers->where('timer_id', $id);
        }

        //filter - stopped timers (usedfor timesheets)
        if (request()->filled('filter_timer_status')) {
            $timers->where('timer_status', request('filter_timer_status'));
        }

        //filter - creator
        if (request()->filled('filter_timer_creatorid')) {
            $timers->where('timer_creatorid', request('filter_timer_creatorid'));
        }

        //filter - client
        if (request()->filled('filter_timer_clientid')) {
            $timers->where('timer_clientid', request('filter_timer_clientid'));
        }

        //filter - project
        if (request()->filled('filter_timer_projectid')) {
            $timers->where('timer_projectid', request('filter_timer_projectid'));
        }

        //filter - project
        if (request()->filled('filter_timer_leadid')) {
            $timers->where('timer_leadid', request('filter_timer_leadid'));
        }

        //filter - users
        if (request()->filled('filter_timer_creatorid') && is_array(request('filter_timer_creatorid'))) {
            $timers->whereIn('timer_creatorid', request('filter_timer_creatorid'));
        }

        //filter - user
        if (request()->filled('filter_timer_creatorid') && is_numeric(request('filter_timer_creatorid'))) {
            $timers->where('timer_creatorid', request('filter_timer_creatorid'));
        }


        //filter: date (start)
        if (request()->filled('filter_date_created_start')) {
            $timers->whereDate('timer_created', '>=', request('filter_date_created_start'));
        }

        //filter: date (end)
        if (request()->filled('filter_date_created_end')) {
            $timers->whereDate('timer_created', '<=', request('filter_date_created_end'));
        }

        //resource filtering
        if (request()->filled('timesheetresource_type') && request()->filled('timesheetresource_id')) {
            switch (request('timesheetresource_type')) {
            case 'client':
                $timers->where('timer_clientid', request('timesheetresource_id'));
                break;
            case 'project':
                $timers->where('timer_projectid', request('timesheetresource_id'));
                break;
            }
        }

        //grouping - tasks
        if (request('filter_grouping') == 'task') {
            $timers->groupBy('timers.timer_taskid');
            $timers->groupBy('timers.timer_creatorid');
        }
        //grouping - user
        if (request('filter_grouping') == 'user') {
            $timers->groupBy('timers.timer_creatorid');
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $timers->where(function ($query) {
                $query->Where('first_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('timer_created', '=', date('Y-m-d', strtotime(request('search_query'))));
                $query->orWhere('project_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('task_title', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('timers', request('orderby'))) {
                $timers->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'user':
                $timers->orderBy('first_name', request('sortorder'));
                break;
            case 'task':
                $timers->orderBy('task_title', request('sortorder'));
                break;
            case 'start_time':
                $timers->orderBy('timer_created', request('sortorder'));
                break;
            case 'stop_time':
                $timers->orderBy('timer_updated', request('sortorder'));
                break;
            case 'time':
                $timers->orderBy('timer_time', request('sortorder'));
                break;
            case 'related':
                $timers->orderBy('lead_title', request('sortorder'));
                $timers->orderBy('project_title', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $timers->orderBy('timer_id', 'desc');
        }

        //eager load
        // $timers->with(['category']);

        // Get the results and return them.
        return $timers->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Search model
     * Deault sorting by 'hours',  'desc'
     * @param int $project_id required
     * @return bool
     */
    public function billableTasks($project_id = '') {

        $timers = $this->timer->newQuery();

        //joins
        $timers->leftJoin('tasks', 'tasks.task_id', '=', 'timers.timer_taskid');

        // all client fields
        $timers->selectRaw('*');

        $timers->selectRaw("GROUP_CONCAT(timer_id SEPARATOR ',') as `timer_ids`");

        //sum the time
        $timers->selectRaw('(SELECT sum(timer_time))
        AS time');

        //for specified project
        $timers->where('timer_projectid', $project_id);

        //stopped timers
        $timers->where('timer_status', 'stopped');

        //not invoiced
        $timers->where('timer_billing_status', 'not_invoiced');

        //group all
        $timers->groupBy('timers.timer_taskid');

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //others
            switch (request('orderby')) {
            case 'task_title':
                $timers->orderBy('task_title', request('sortorder'));
                break;
            case 'time':
                $timers->orderBy('time', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $timers->orderBy('time', 'desc');
        }

        //eager load
        // $timers->with(['category']);

        // Get the results and return them.
        return $timers->get();
    }

    /**
     * Search model
     * Deault sorting by 'hours',  'desc'
     * @param array $data required
     *            - timer_projectid - project id
     *            - timer_billing_status (invoiced | not_invoiced | all)
     *            - return (human_readable | seconds)
     * @return bool
     */
    public function projectLoggedHours($data = []) {

        $hours = 0;
        $minutes = 0;
        $time = 0;

        //validation
        if (!isset($data['timer_projectid']) || !isset($data['timer_billing_status']) || !isset($data['return'])) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        $timers = $this->timer->newQuery();

        // all client fields
        $timers->selectRaw('*');

        //sum the time
        $timers->selectRaw('(SELECT sum(timer_time)) AS time');

        //for specified project
        $timers->where('timer_projectid', $data['timer_projectid']);

        if ($data['timer_billing_status'] != 'all') {
            $timers->where('timer_billing_status', $data['timer_billing_status']);
        }

        //group all
        $timers->groupBy('timers.timer_projectid');

        // Get the results and return them.
        $timer = $timers->first();

        //return seconds
        if ($data['return'] == 'seconds') {
            if ($timer) {
                return $timer->time;
            } else {
                return 0;
            }
        }

        //return 10Hr 35Mins
        if ($data['return'] == 'human_readable') {
            if ($timer) {
                //get hours and minutes
                $hours = runtimeSecondsWholeHours($timer->time);
                $minutes = runtimeSecondsWholeMinutes($timer->time);
            }
            return $hours . ' ' . strtolower(__('lang.hrs')) . ' : ' . $minutes . ' ' . strtolower(__('lang.mins'));
        }

    }

    /**
     * start a timer for a user, for a given task. If timer does not exist, create one
     *  @param object $task the task object
     * @return bool
     */
    public function createTimer($task) {

        if (!$task instanceof \App\Models\Task) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
        //get existig users timer or create a new one
        $timer = new $this->timer;
        $timer->timer_taskid = $task->task_id;
        $timer->timer_projectid = $task->task_projectid;
        $timer->timer_clientid = $task->task_clientid;
        $timer->timer_started = \Carbon\Carbon::now()->timestamp;
        $timer->timer_creatorid = auth()->id();
        $timer->save();

        return true;
    }

    /**
     * stop running timers. Can be for a set user | set task | project | all timers
     *  @param array $data (user_id, task_id)
     * @return bool
     */
    public function stopRunningTimers($data = []) {

        //validation
        if (!is_array($data)) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //new query
        $timer = $this->timer->newQuery();

        //running timmers
        $timer->where('timer_status', 'running');

        //limit by the user id
        if (isset($data['timer_creatorid']) && is_numeric($data['timer_creatorid'])) {
            $timer->where('timer_creatorid', $data['timer_creatorid']);
        }

        //limit by whole project
        if (isset($data['timer_projectid']) && is_numeric($data['timer_projectid'])) {
            $timer->where('timer_projectid', $data['timer_projectid']);
        }

        //limit by the task id
        if (isset($data['timer_taskid']) && is_numeric($data['timer_taskid'])) {
            $timer->where('timer_taskid', $data['timer_taskid']);
        }

        //get all timers that we have just stopped
        $timers = $timer->get();

        //update the cumulative time for each timer
        foreach ($timers as $record) {
            //upsate and stop the timer
            $record->timer_stopped = \Carbon\Carbon::now()->timestamp;
            $record->timer_time = \Carbon\Carbon::now()->timestamp - $record->timer_started;
            $record->timer_status = 'stopped';
            $record->save();
        }
    }

    /**
     * update all running timers (current time) for a given task
     * @return bool
     */
    public function updateRunningTimers($taskid = '') {

        //validation
        if (!is_numeric($taskid)) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $timer = $this->timer->newQuery();

        //get the timers
        $timer->where('timer_taskid', $taskid);
        $timer->where('timer_status', 'running');
        $timers = $timer->get();

        //stop timer & update running time
        foreach ($timers as $record) {
            $record->timer_time = \Carbon\Carbon::now()->timestamp - $record->timer_started;
            $record->save();
        }
    }

    /**
     * update a users running timer
     * @param int $userid
     * @return bool
     */
    public function updateUsersRunningTimer($userid = '') {

        //validation
        if (!is_numeric($userid)) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $timer = $this->timer->newQuery();

        //get the timers
        $timer->where('timer_status', 'running');
        $timer->where('timer_creatorid', $userid);
        $timers = $timer->get();

        //stop timer & update running time
        foreach ($timers as $record) {
            $record->timer_time = \Carbon\Carbon::now()->timestamp - $record->timer_started;
            $record->save();
        }
    }

    /**
     * sum timers for a given task
     *  - can be for all users assigned to the task or for a specified user
     * @return bool
     */
    public function sumTimers($taskid = '', $userid = '') {

        //validation
        if (!is_numeric($taskid)) {
            Log::error("validation error - required information is missing", ['process' => '[TimerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //first update running timers
        $this->updateRunningTimers($taskid);

        $timer = $this->timer->newQuery();

        //sum all timers for this task
        $timer->selectRaw('(SELECT COALESCE(SUM(timer_time), 0)) AS sum_time');

        //limit by task
        $timer->where('timer_taskid', $taskid);

        //limit by a user
        if (is_numeric($userid)) {
            $timer->where('timer_creatorid', $userid);
        }

        //get
        $time = $timer->first();

        //return the time (in seconds)
        return ($time->sum_time);

    }

}