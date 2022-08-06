<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [delete attachment] precheck processes for tasks
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tasks;
use App\Models\Task;
use App\Permissions\AttachmentPermissions;
use Closure;
use Log;

class DeleteAttachment {

    /**
     * The permisson repository instance.
     */
    protected $attachmentpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(AttachmentPermissions $attachmentpermissions) {

        //permissions
        $this->attachmentpermissions = $attachmentpermissions;

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
            Log::error("attachment could not be found", ['process' => '[permissions][tasks][delete-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
            //just return
            return response()->json([]);
        }

        //check permissions
        if ($this->attachmentpermissions->check('download', $attachment->attachment_id)) {
            return $next($request);
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][tasks][delete-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
        abort(403);
    }
}