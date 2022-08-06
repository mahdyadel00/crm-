<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Knowledgebase;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] knowledgebases
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.knowledgebase')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }
        
        //knowledgebase id
        $knowledgebase_id = $request->route('kb');

        //does the knowledgebase exist
        if ($knowledgebase_id == '' || !$knowledgebase = \App\Models\Knowledgebase::Where('knowledgebase_id', $knowledgebase_id)->first()) {
            Log::error("knowledgebase could not be found", ['process' => '[permissions][knowledgebases][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'knowledgebase id' => $knowledgebase_id ?? '']);
            abort(409, __('lang.article_not_found'));
        }

        //permission: does user have permission edit knowledgebases
        if (auth()->user()->role->role_knowledgebase >= 2) {
            
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][knowledgebase][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
