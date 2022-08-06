<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Settings;

use App\Models\Settings;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] settings
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //permission: does user have permission for settings
        if (auth()->user()->is_admin) {

            //visibility settings
            request()->merge([
                'visibility_left_menu_toggle_button' => '',
                'visibility_settings_left_menu_toggle_button' => 'visible',
                'dashboard_section' => 'settings'
            ]);

            return $next($request);
        }

        //NB: client db/repository (clientid filter merege) is applied in main controller.php

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][settings][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
