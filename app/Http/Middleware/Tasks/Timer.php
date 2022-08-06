<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [timer] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Timer {

    /**
     * The task permisson repository instance.
     */
    protected $task_permissions;

    /**
     * The project permisson repository instance.
     */
    protected $project_permissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(TaskPermissions $task_permissions, ProjectPermissions $project_permissions) {
        $this->task_permissions = $task_permissions;
        $this->project_permissions = $project_permissions;
    }

    /**
     * This middleware does the following
     *   1. validates that the task exists
     *   2. checks users permissions to stop or start [timers]
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //task id
        $task_id = $request->route('id');

        //action
        $action =  request()->segment(4);


        //validate
        if(!in_array($action, ['start','stop','stopall'])){
            Log::error("validation error - missing action", ['process' => '[permissions][task][timers]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '', 'user_id' => auth()->id()]);
            abort(409);
        }

        //does the task exist
        if (!$task = \App\Models\Task::Where('task_id', $task_id)->first()) {
            Log::error("task could not be found", ['process' => '[permissions][task][timers]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '', 'user_id' => auth()->id()]);
            abort(404);
        }

        //[team only]
        if (!auth()->user()->is_team) {
            Log::error("permission denied", ['process' => '[permissions][tasks][timers]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '', 'user_id' => auth()->id()]);
            abort(403);
        }

        //[stop-all] all timers for a specific task
        if ($action == 'stopall') {
            if ($this->project_permissions->check('super-user', $task->task_projectid)) {
                return $next($request);
            } else {
                Log::error("permission denied", ['process' => '[permissions][tasks][timers]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '', 'user_id' => auth()->id()]);
                abort(403);
            }
        }

        //[start][stop] a timer for a specific task
        if (in_array($action, ['start', 'stop'])) {
            if ($this->task_permissions->check('timers', $task_id)) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tasks][timers]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '', 'user_id' => auth()->id()]);
        abort(403);
    }
}
