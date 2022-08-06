<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for project templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Templates\Projects;

use App\Models\Project;
use Closure;
use Log;

class Index {

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] projects
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //various frontend and visibility settings
        $this->fronteEnd();

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_templates_projects >= 1) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[project templates][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        request()->merge([
            'resource_query' => 'ref=list',
        ]);

        //default show some table columns
        config([
            'visibility.projects_col_client' => true,
            'visibility.projects_col_project' => true,
            'visibility.projects_col_payments' => true,
        ]);

        //permissions -adding
        if (auth()->user()->role->role_templates_projects >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.projects_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_templates_projects >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }
    }
}
