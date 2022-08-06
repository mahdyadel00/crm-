<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Foo;
use Closure;
use Log;

class DestroyBasic {

    /**
     * This middleware does the following
     *   1. validates that the foo exists
     *   2. checks users permissions to [delete] the foo
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //foo id
        $foo_id = $request->route('foo');

        //does the foo exist
        if ($foo_id == '' || !$foo = \App\Models\Foo::Where('foo_id', $foo_id)->first()) {
            abort(404);
        }

        //permission: does user have permission to delete this foo
        if ($this->foo_permissions->check('delete', $foo)) {           
            return $next($request);
        }

        //team: does user have permission delete foos
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_foos >= 3) {        
                return $next($request);
            }
        }

        //client: does user have permission delete foos
        if (auth()->user()->is_client) {
            if ($foo->foo_clientid == auth()->user()->clientid) {
                if (auth()->user()->role->role_foos >= 3) {         
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[foos][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'foo id' => $foo_id ?? '']);
        abort(403);
    }
}
