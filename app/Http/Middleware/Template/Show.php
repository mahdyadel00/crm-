<?php

namespace App\Http\Middleware\Foo;
use Closure;
use Log;

class Show {

    /**
     * This middleware does the following
     *   1. validates that the foo exists
     *   2. checks users permissions to [view] the foo
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
            abort(404);
        }

        //team: does user have permission edit foos
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_foos >= 1) {
                return $next($request);
            }
        }

        //client: does user have permission edit foos
        if (auth()->user()->is_client) {
            if ($foo->foo_clientid == auth()->user()->clientid) {
                if (auth()->user()->role->role_foos >= 1) {
                    return $next($request);
                }
            }
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[foos][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default: show client and project options
        config(['visibility.foo_modal_client_fields' => true]);

        //merge data
        request()->merge([
            'resource_query' => 'ref=page',
        ]);
    }

}
