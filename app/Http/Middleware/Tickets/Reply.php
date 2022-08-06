<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [reply] precheck processes for tickets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Tickets;
use Closure;
use Log;

class Reply {

    /**
     * This middleware does the following
     *   1. add various needed data into the request
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
            abort(404);
        }

        //add ticket it to request
        request()->merge([
            'ticketreply_ticketid' => $request->route('ticket'),
        ]);

        //make sure it not on hold
        if ($ticket->ticket_status == 'on_hold') {
            abort(409, __('lang.ticket_is_on_hold'));
        }

        //update status
        if (auth()->user()->is_team) {
            request()->merge([
                'ticket_status' => 'answered',
            ]);
        }

        //update status
        if (auth()->user()->is_client) {
            request()->merge([
                'ticket_status' => 'open',
            ]);
        }

        return $next($request);
    }
}
