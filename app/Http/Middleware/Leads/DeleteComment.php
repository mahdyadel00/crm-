<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [delete comment] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;
use App\Models\Lead;
use App\Permissions\CommentPermissions;
use Closure;
use Log;

class DeleteComment {

    /**
     * The permisson repository instance.
     */
    protected $commentpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(CommentPermissions $commentpermissions) {

        //permissions
        $this->commentpermissions = $commentpermissions;

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
        $comment_id = $request->route('commentid');

        //does the lead exist
        if (!$comment = \App\Models\Comment::Where('comment_id', $comment_id)->first()) {
            Log::error("comment could not be found", ['process' => '[permissions][leads][delete-comment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'comment id' => $comment_id ?? '']);
            //just return
            return response()->json([]);
        }

        //check permissions
        if ($comment->commentresource_type == 'lead') {
            if ($this->commentpermissions->check('delete', $comment_id)) {
                return $next($request);
            }
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][leads][delete-comment]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'comment id' => $comment_id ?? '']);
        abort(403);
    }
}