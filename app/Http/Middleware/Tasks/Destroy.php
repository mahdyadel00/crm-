<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;
use App\Models\Task;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Destroy {

    /**
     * The permisson repository instance.
     */
    protected $taskpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(TaskPermissions $taskpermissions, Task $task_model) {

        //task permissions repo
        $this->taskpermissions = $taskpermissions;

    }

    /**
     * Check user permissions to destroy a task
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.tasks')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //for a single item request - merge into an $ids[x] array and set as if checkox is selected (on)
        if (is_numeric($request->route('task'))) {
            $ids[$request->route('task')] = 'on';
            request()->merge([
                'ids' => $ids,
            ]);
        }

        //loop through each task and check permissions
        if (is_array(request('ids'))) {

            //validate each item in the list exists
            foreach (request('ids') as $id => $value) {
                //only checked items
                if ($value == 'on') {
                    //validate
                    if (!$task = \App\Models\Task::Where('task_id', $id)->first()) {
                        abort(409, __('lang.one_of_the_selected_items_nolonger_exists'));
                    }
                }
                //permission on each one
                if ($task->task_projectid > 0) {
                    //project task
                    if (!$this->taskpermissions->check('delete', $id)) {
                        abort(403, __('lang.permission_denied_for_this_item') . " - #$id");
                    }
                } else {
                    //template task
                    if (auth()->user()->role->role_templates_projects < 2) {
                        abort(403, __('lang.permission_denied_for_this_item') . " - #$id");
                    }
                }
            }
        } else {
            //no items were passed with this request
            Log::error("no items were sent with this request", ['process' => '[permissions][tasks][change-category]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'task id' => $task_id ?? '']);
            abort(409);
        }

        //all is on - passed
        return $next($request);
    }
}
