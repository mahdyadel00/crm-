<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for task statuses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\TaskStatus;
use Log;

class TaskStatusRepository {

    /**
     * The tasks repository instance.
     */
    protected $status;

    /**
     * Inject dependecies
     */
    public function __construct(TaskStatus $status) {
        $this->status = $status;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object task status collection
     */
    public function search($id = '') {

        $status = $this->status->newQuery();

        //joins
        $status->leftJoin('users', 'users.id', '=', 'tasks_status.taskstatus_creatorid');

        // all client fields
        $status->selectRaw('*');

        //count tasks
        $status->selectRaw('(SELECT COUNT(*)
                                      FROM tasks
                                      WHERE task_status = tasks_status.taskstatus_id)
                                      AS count_tasks');
        if (is_numeric($id)) {
            $status->where('taskstatus_id', $id);
        }

        //default sorting
        $status->orderBy('taskstatus_position', 'asc');

        // Get the results and return them.
        return $status->paginate(10000);
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function update($id) {

        //get the record
        if (!$status = $this->status->find($id)) {
            return false;
        }

        //general
        $status->taskstatus_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('taskstatus_title'));
        $status->taskstatus_color = request('taskstatus_color');

        //save
        if ($status->save()) {
            return $status->taskstatus_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[TaskStatusRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @param int $position position of new record
     * @return mixed object|bool
     */
    public function create($position = '') {

        //validate
        if (!is_numeric($position)) {
            Log::error("error creating a new task status record in DB - (position) value is invalid", ['process' => '[create()]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save
        $status = new $this->status;

        //data
        $status->taskstatus_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('taskstatus_title'));
        $status->taskstatus_color = request('taskstatus_color');
        $status->taskstatus_creatorid = auth()->id();
        $status->taskstatus_position = $position;

        //save and return id
        if ($status->save()) {
            return $status->taskstatus_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[TaskStatusRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
    

}