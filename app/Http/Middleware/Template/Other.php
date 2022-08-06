<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [other] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Foo;
use App\Permissions\TaskPermissions;
use Closure;
use Log;

class Other {

    /**
     * The permisson repository instance.
     */
    protected $permissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(TaskPermissions $permissons) {
        $this->permissons = $permissons;
    }

    /**
     * This middleware does the following
     *   1. validates that the foo exists
     *   2. checks users permissions to [edit] the foo
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
        if (!$foo = \App\Models\Foo::Where('foo_id', $foo_id)->first()) {
            Log::error("foo could not be found", ['process' => '[foos][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'category id' => $category_id ?? '']);
            abort(404);
        }

        //permission: does user have permission to edit this foo
        if ($this->permissions->check('delete', $foo_id)) {   
            return $next($request);
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[foos][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
