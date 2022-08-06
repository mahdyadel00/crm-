<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for product proposals
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Proposals;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] proposals
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //proposal id
        $doc_id = $request->route('proposal');

        //frontend
        $this->fronteEnd();

        //does the proposal exist
        if ($doc_id == '' || !$proposal = \App\Models\Proposal::Where('doc_id', $doc_id)->first()) {
            Log::error("proposal could not be found", ['process' => '[permissions][proposals][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'proposal id' => $doc_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit proposals
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_proposals >= 2) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][proposals][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //permissions
        if (auth()->user()->is_team) {
            //editing
            if (auth()->user()->role->role_proposals >= 2) {
                config([
                    'visibility.document_options_buttons' => true,
                    'visibility.document_edit_estimate_button' => true,
                    'visibility.document_edit_variables_button' => true,
                ]);
            }
            //deleting
            if (auth()->user()->role->role_proposals >= 3) {
                config([
                    'visibility.delete_document_button' => true,
                ]);
            }
        }
    }
}
