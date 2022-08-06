<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [download attachment] precheck processes for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Expenses;
use Closure;
use Log;

class DownloadAttachment {

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
                if (auth()->user()->role->role_expenses >= 1) {
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

            //client - do they have permission to view project expenses and hence view/download expense attachments
            if (auth()->user()->is_client) {
                if ($expense = \App\Models\Expense::Where('expense_id', $attachment->attachmentresource_id)->first()) {
                    if ($project = \App\Models\Project::Where('project_id', $expense->expense_projectid)->first()) {
                        if ($project->project_clientid == auth()->user()->clientid && $project->clientperm_expenses_view == 'yes') {
                            return $next($request);
                        }
                    }
                }
            }

        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][attachments][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment id' => $attachment_id ?? '']);
        abort(403);
    }
}
