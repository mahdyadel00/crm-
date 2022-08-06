<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Knowledgebase;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the knowledgebase exists
     *   2. checks users permissions to [view] the knowledgebase
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
        $knowledgebase_slug = request()->route('slug');

        //does the knowledgebase exist
        if ($knowledgebase_slug == '' || !$article = \App\Models\Knowledgebase::Where('knowledgebase_slug', $knowledgebase_slug)->first()) {
            abort(404);
        }

        //permission: does user have permission edit knowledgebases
        if (auth()->user()->role->role_knowledgebase >= 1) {
            //add slug to request
            request()->merge([
                'knowledgebase_slug' => $knowledgebase_slug,
            ]);
            
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][knowledgebases][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'knowledgebase id' => $knowledgebase_id ?? '']);
        abort(403);
    }
}
