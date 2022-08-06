<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [download] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;
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
        if (!config('visibility.modules.tickets')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //attachment id
        $uniqueid = $request->route('uniqueid');

        //does the attachment exist
        if (!$attachment = \App\Models\Attachment::Where('attachment_uniqiueid', $uniqueid)->first()) {
            Log::error("attachment could not be found", ['process' => '[permissions][attachment][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'attachment id' => $category_id ?? '']);
            abort(409, __('lang.file_not_found'));
        }

        //permission: does user have permission to download the attachment
        if (in_array($attachment->attachmentresource_type, ['ticket', 'ticketreply'])) {

            //team : do they have permission download attachemnts on this ticket
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_tickets >= 1) {
                    return $next($request);
                }
            }

            //client - do they have permission to download the attachement
            if (auth()->user()->is_client) {
                if ($attachment->attachmentresource_type == 'ticket') {
                    if ($ticket = \App\Models\Ticket::Where('ticket_id', $attachment->attachmentresource_id)->first()) {
                        if ($ticket->ticket_clientid == auth()->user()->clientid) {
                            return $next($request);
                        }
                    }
                }
                if ($attachment->attachmentresource_type == 'ticketreply') {
                    if ($reply = \App\Models\TicketReply::Where('ticketreply_id', $attachment->attachmentresource_id)->first()) {
                        if ($reply->ticketreply_clientid == auth()->user()->clientid) {
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
