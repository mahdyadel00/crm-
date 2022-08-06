<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [delete attachment] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;
use App\Models\Lead;
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
     * Check user permissions to edit a lead
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.leads')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //attachement id
        $attachment_uniqueid = $request->route('uniqueid');

        //does the lead exist
        if (!$attachment = \App\Models\Attachment::Where('attachment_uniqiueid', $attachment_uniqueid)->first()) {
            Log::error("attachment could not be found", ['process' => '[permissions][leads][delete-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
            //just return
            return response()->json([]);
        }

        //check permissions
        if ($this->attachmentpermissions->check('download', $attachment->attachment_id)) {
            return $next($request);
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][leads][delete-attachment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment unique id' => $attachment_uniqueid ?? '']);
        abort(403);
    }
}