<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [delete attachement] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use Closure;
use Log;

class DeleteAttachment {

    /**
     * This middleware does the following
     *   1. validates that the attachment exists
     *   2. checks users permissions to [edit] the attachment
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.expenses')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //attachment id
        $uniqueid = $request->route('uniqueid');

        //does the attachment exist
        if (!$attachment = \App\Models\Attachment::Where('attachment_uniqiueid', $uniqueid)->first()) {
            Log::error("attachment could not be found", ['process' => '[permissions][attachment][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment id' => $category_id ?? '']);
            abort(404);
        }

        //permission: does user have permission to download the attachment
        if ($attachment->attachmentresource_type == 'expense') {

            //team : do they have permission download attachemnts on this expense
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_expenses >= 2) {
                    if (auth()->user()->role->role_expenses_scope == 'global') {
                        return $next($request);
                    }
                    if (auth()->user()->role->role_expenses_scope == 'own') {
                        if ($attachment->attachment_creatorid == auth()->id()) {
                            return $next($request);
                        }
                    }
                }
            }

        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][attachments][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment id' => $attachment_id ?? '']);
        abort(403);
    }
}
