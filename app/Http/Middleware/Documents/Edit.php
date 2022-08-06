<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Documents;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] foos
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //documents id
        $document_id = $request->route('document');

        //DOC NOT SPECIFIED
        if (!request()->filled('doc_type')) {
            Log::error("error editing a proposal or a contract. the document type was not specified in the request", ['process' => '[documents][edit]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //PROPOSALS
        if (request('doc_type') == 'proposal') {

            //does the documents exist
            if ($document_id == '' || !$document = \App\Models\Proposal::Where('doc_id', $document_id)->first()) {
                Log::error("proposal could not be found", ['process' => '[documents][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'id' => $document_id ?? '']);
                abort(404);
            }

            //permission: does user have permission edit foos
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_proposals >= 2) {
                    return $next($request);
                }
            }
        }

        //CONTRACTS
        if (request('doc_type') == 'contract') {

            //does the documents exist
            if ($document_id == '' || !$document = \App\Models\Contract::Where('doc_id', $document_id)->first()) {
                Log::error("contract could not be found", ['process' => '[documents][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'id' => $document_id ?? '']);
                abort(404);
            }

            //permission: does user have permission edit foos
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_contracts >= 2) {
                    return $next($request);
                }
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][foos][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

}