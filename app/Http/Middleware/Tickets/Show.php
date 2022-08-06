<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the ticket exists
     *   2. checks users permissions to [view] the ticket
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
            abort(404);
        }

        //frontend
        $this->fronteEnd($ticket);

        //team: does user have permission edit tickets
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tickets >= 1) {
                return $next($request);
            }
        }

        //client: does user have permission edit tickets
        if (auth()->user()->is_client) {
            if ($ticket->ticket_clientid == auth()->user()->clientid) {
                if (auth()->user()->role->role_tickets >= 1) {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][tickets][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'ticket id' => $ticket_id ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($ticket) {

        //permissions -viewing
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_tickets >= 2) {
                config([
                    //visibility
                    'visibility.ticket_replying' => true,
                    'visibility.action_buttons_edit' => true,
                ]);
            }
        }

        //client user
        if (auth()->user()->is_client) {
            config([
                //visibility
                'visibility.ticket_replying' => true,
            ]);
        }

        //on hold tickets
        if ($ticket->ticket_status == 'on_hold') {
            config([
                //visibility
                'visibility.ticket_replying' => false,
                'visibility.ticket_replying_on_hold' => true,
            ]);
        }

    }

}
