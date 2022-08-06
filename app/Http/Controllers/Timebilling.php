<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for time billing
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Timebilling\IndexResponse;
use App\Repositories\TaskRepository;
use App\Repositories\TimerRepository;

class Timebilling extends Controller {

    public function __construct(
        TaskRepository $taskrepo,
        TimerRepository $timerrepo
    ) {

        //parent
        parent::__construct();

        $this->taskrepo = $taskrepo;
        $this->timerrepo = $timerrepo;

        //authenticated
        $this->middleware('auth');
    }

    /**
     * show all billable tasks
     * @param int $id task id
     * @return \Illuminate\Http\Response
     */
    public function index($id) {

        //default billing rate
        $billing_rate = 0;

        //timestap
        $timestamp = \Carbon\Carbon::now()->timestamp;;

        //get team members
        $tasks = $this->timerrepo->billableTasks($id);

        //get the project hourly rate
        if ($project = \App\Models\Project::Where('project_id', $id)->first()) {
            if (is_numeric($project->project_billing_rate)) {
                $billing_rate = $project->project_billing_rate;
            }
        }

        //workout total for each item
        foreach ($tasks as $task) {

            //defaults
            $hours_total = 0;
            $minutes_total = 0;

            //get hours and minutes
            $task->hours = runtimeSecondsWholeHours($task->time);
            $task->minutes = runtimeSecondsWholeMinutes($task->time);

            //total for hours
            if ($task->hours > 0 && $billing_rate > 0) {
                $hours_total = ($task->hours * $billing_rate);
            }

            //total for minutes
            if ($task->minutes > 0 && $billing_rate > 0) {
                $minutes_total = ($task->minutes * $billing_rate / 60);
            }

            //final
            $task->total = $hours_total + $minutes_total;

            //tracking timestamp
            $task->timestamp = $timestamp;

        }

        //reponse payload
        $payload = [
            'tasks' => $tasks,
            'billing_rate' => $billing_rate,
            'project_id' => $id,
            'count' => $tasks->count(),
        ];

        //show the view
        return new IndexResponse($payload);
    }

}