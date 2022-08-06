<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for product items
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Items;
use Closure;
use Log;

class Edit {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] items
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //item id
        $item_id = $request->route('item');

        //frontend
        $this->fronteEnd();

        //does the item exist
        if ($item_id == '' || !$item = \App\Models\Item::Where('item_id', $item_id)->first()) {
            Log::error("item could not be found", ['process' => '[permissions][items][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'item id' => $item_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit items
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_items >= 2) {
                
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][items][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

    }
}
