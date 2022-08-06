<?php

namespace App\Http\Middleware\Proposals;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the proposal exists
     *   2. checks users permissions to [view] the proposal
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //proposal id
        $doc_id = $request->route('proposal');

        //does the proposal exist
        if ($doc_id == '' || !$proposal = \App\Models\Proposal::Where('doc_id', $doc_id)->first()) {
            abort(404);
        }

        //frontend
        $this->fronteEnd($proposal);

        //team: does user have permission edit proposals
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_proposals >= 1) {
                return $next($request);
            }
        }

        //client: does user have permission edit invoices
        if (auth()->user()->is_client) {
            //404 for invoices in draft status
            if ($proposal->doc_status == 'draft') {
                abort(404);
            }
            //ok
            if ($proposal->doc_client_id == auth()->user()->clientid) {
                return $next($request);
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[proposals][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($proposal = '') {

        //permissions
        if (auth()->user()->is_team) {
            //editing
            if (auth()->user()->role->role_proposals >= 2) {
                config([
                    'visibility.document_options_buttons' => true,
                    'visibility.document_edit_button' => true,
                ]);
            }
            //deleting
            if (auth()->user()->role->role_proposals >= 3) {
                config([
                    'visibility.delete_document_button' => true,
                ]);
            }
        }

        if (auth()->user()->is_client) {
            //footer buttons - footer - (generally)
            config([
                'visibility.proposal_accept_decline_button_footer' => true,
            ]);

            //accept of decline buttons
            if(in_array($proposal->doc_status, ['new', 'revised'])){
                config([
                    'visibility.proposal_accept_decline_buttons' => true,
                ]);
            }
        }

    }

}