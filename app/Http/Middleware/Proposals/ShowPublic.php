<?php

namespace App\Http\Middleware\Proposals;
use Closure;

class ShowPublic {

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

        //validation of url
        if (!$proposal = \App\Models\Proposal::Where('doc_unique_id', $request->route('proposal'))->first()) {
            abort(404);
        }

        //frontend
        $this->fronteEnd($proposal);

        return $next($request);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($proposal = '') {

        //footer buttons - header - (generally)
        config([
            'visibility.proposal_accept_decline_button_header' => true,
        ]);

        //accept of decline buttons
        if (in_array($proposal->doc_status, ['new', 'revised'])) {
            config([
                'visibility.proposal_accept_decline_buttons' => true,
            ]);
        }
    }

}