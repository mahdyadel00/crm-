<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [download attachment] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;
use App\Models\Task;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class DownloadAttachment {

    /**
     * The permisson repository instance.
     */
    protected $taskpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(TaskPermissions $taskpermissions) {

        //permissions
        $this->taskpermissions = $taskpermissions;

    }

    /**
     * Check user permissions to edit a task
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //attachement id
        $attachment_uniqueid = $request->route('uniqueid');

        //does the task exist
        if (!$attachment = \App\Models\Attachment::Where('attachment_uniqiueid', $attachment_uniqueid)->first()) {
            Log::error("attachment could not be found", ['process' => '[permissions][tasks][download-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
            abort(404);
        }

        //validate that its a project task
        if ($attachment->attachmentresource_type == 'task') {
            //get the task
            if ($task = \App\Models\Task::Where('task_id', $attachment->attachmentresource_id)->first()) {
                //view task permissions
                if ($this->taskpermissions->check('view', $task->task_id)) {
                    return $next($request);
                }
            }
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][tasks][download-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
        abort(403);
    }
}