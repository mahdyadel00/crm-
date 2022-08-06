<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Foo;
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

        //foo id
        $foo_id = $request->route('foo');

        //frontend
        $this->fronteEnd();

        //does the foo exist
        if ($foo_id == '' || !$foo = \App\Models\Foo::Where('foo_id', $foo_id)->first()) {
            Log::error("foo could not be found", ['process' => '[foos][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'foo id' => $foo_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit foos
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_foos >= 2) {
                //global permissions
                if (auth()->user()->role->role_foos_scope == 'global') {
                    
                    return $next($request);
                }
                //own permissions
                if (auth()->user()->role->role_foos_scope == 'own') {
                    if ($foo->foo_creatorid == auth()->id()) { 
                        return $next($request);
                    }
                }
            }
        }

        //client: does user have permission edit foos
        if (auth()->user()->is_client) {
            if ($foo->foo_clientid == auth()->user()->clientid) {
                if (auth()->user()->role->role_foos >= 2) {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][foos][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //some settings
        config([
            'settings.foo' => true,
            'settings.bar' => true,
        ]);
    }
}
