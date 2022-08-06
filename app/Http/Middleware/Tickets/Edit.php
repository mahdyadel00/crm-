<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] tickets
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

        //ticket id
        $ticket_id = $request->route('ticket');

        //does the ticket exist
        if ($ticket_id == '' || !$ticket = \App\Models\Ticket::Where('ticket_id', $ticket_id)->first()) {
            Log::error("ticket could not be found", ['process' => '[permissions][tickets][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'ticket id' => $ticket_id ?? '']);
            abort(409, __('lang.ticket_not_found'));
        }

        //permission: does user have permission edit tickets
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tickets >= 2) {
                return $next($request);
            }
        }

        //client: does user have permission edit tickets
        if (auth()->user()->is_client) {
            if ($ticket->ticket_clientid == auth()->user()->clientid) {
                return $next($request);
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tickets][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
