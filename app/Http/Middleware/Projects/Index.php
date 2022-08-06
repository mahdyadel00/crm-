<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [index] precheck processes for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Projects;

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

        //validate module status
        if (!config('visibility.modules.projects')) {
            abort(404);
            return $next($request);
        }

        //various frontend and visibility settings
        $this->fronteEnd();

        //set the users current view
        $this->toggleView();

        //embedded request: limit by supplied resource data
        if (request()->filled('projectresource_type') && request()->filled('projectresource_id')) {
            //project projects
            if (request('projectresource_type') == 'project') {
                request()->merge([
                    'filter_project_projectid' => request('projectresource_id'),
                ]);
            }
            //client projects
            if (request('projectresource_type') == 'client') {
                request()->merge([
                    'filter_project_clientid' => request('projectresource_id'),
                ]);
            }
        }

        //admin user permission
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_projects >= 1) {
                //[limit] - for users with only local level scope
                if (auth()->user()->role->role_projects_scope == 'own') {
                    request()->merge(['filter_my_projects' => array(auth()->id())]);
                }
                //toggle 'my projects' button opntions
                $this->toggleOwnFilter();

                return $next($request);
            }
        }

        //client users
        if (auth()->user()->is_client) {

            //show all projects
            request()->merge([
                'filter_show_archived_projects' => 'yes',
            ]);
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][projects][index]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //default show some table columns
        config([
            'visibility.projects_col_client' => true,
            'visibility.projects_col_category' => true,
            'visibility.projects_col_team' => true,
            'visibility.projects_col_checkboxes' => true,
            'visibility.filter_panel_client_project' => true,
            'visibility.filter_panel_assigned' => true,
        ]);

        /**
         * shorten resource type and id (for easy appending in blade templates)
         * [usage]
         *   replace the usual url('project') with urlResource('project'), in blade templated
         * */
        if (request('projectresource_type') != '' || is_numeric(request('projectresource_id'))) {
            request()->merge([
                'resource_query' => 'ref=list&projectresource_type=' . request('projectresource_type') . '&projectresource_id=' . request('projectresource_id'),
            ]);
        } else {
            request()->merge([
                'resource_query' => 'ref=list',
            ]);
        }

        //permissions -viewing
        if (auth()->user()->role->role_projects >= 1) {
            if (auth()->user()->is_team) {
                config([
                    //visibility
                    'visibility.list_page_actions_filter_button' => true,
                    'visibility.list_page_actions_search' => true,
                    'visibility.stats_toggle_button' => true,
                ]);
            }
            if (auth()->user()->is_client) {
                config([
                    //visibility
                    'visibility.list_page_actions_search' => true,
                    'visibility.projects_col_client' => false,
                ]);
            }
        }

        //permissions -adding
        if (auth()->user()->role->role_projects >= 2) {
            config([
                //visibility
                'visibility.list_page_actions_add_button' => true,
                'visibility.action_buttons_edit' => true,
                'visibility.projects_col_checkboxes' => true,
            ]);
        }

        //permissions -deleting
        if (auth()->user()->role->role_projects >= 3) {
            config([
                //visibility
                'visibility.action_buttons_delete' => true,
            ]);
        }

        //columns visibility
        if (request('projectresource_type') == 'client') {
            config([
                //visibility
                'visibility.projects_col_client' => false,
                'visibility.filter_panel_client_project' => false,
                'visibility.projects_col_category' => false,
                'visibility.projects_col_team' => false,
                'visibility.filter_panel_clients_projects' => true,
            ]);
        }

        //visibility of 'filter assigned" in filter panel
        if (auth()->user()->is_team) {
            if (auth()->user()->role->role_projects_scope == 'global') {
                config([
                    'visibility.filter_panel_assigned' => true,
                ]);
            }
            //hide is users is viewing own projects
            if (auth()->user()->pref_filter_own_projects == 'yes') {
                config([
                    'visibility.filter_panel_assigned' => true,
                ]);
            }
        }

        //cover images - editing
        if(config('system.settings_projects_cover_images') =='enabled' && auth()->user()->role->role_projects_scope == 'global' && auth()->user()->role->role_projects > 1){
            config([
                'visibility.edit_card_cover_image' => true,
            ]);
        }

        //show cover images
        if(config('system.settings_projects_cover_images') =='enabled'){
            config([
                'visibility.card_cover_image' => true,
            ]);
        }



    }

    function toggleOwnFilter() {

        //visibility of 'my leads" button - only users with globa scope need this button
        if (auth()->user()->role->role_projects_scope == 'global') {
            config([
                //visibility
                'visibility.own_projects_toggle_button' => true,
            ]);
        }

        //show toggle archived projects button
        if (auth()->user()->is_team) {
            config([
                'visibility.archived_projects_toggle_button' => true,
            ]);
        }

        //update 'own projects filter'
        if (request('toggle') == 'pref_filter_own_projects') {
            //toggle database settings
            auth()->user()->pref_filter_own_projects = (auth()->user()->pref_filter_own_projects == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //update 'archived projects filter'
        if (request('toggle') == 'pref_filter_show_archived_projects') {
            //toggle database settings
            auth()->user()->pref_filter_show_archived_projects = (auth()->user()->pref_filter_show_archived_projects == 'yes') ? 'no' : 'yes';
            auth()->user()->save();
        }

        //a filter panel search has been done with assigned - so reset 'my projects' to 'no'
        if (request()->filled('filter_assigned')) {
            if (auth()->user()->pref_filter_own_projects == 'yes') {
                auth()->user()->pref_filter_own_projects = 'no';
                auth()->user()->save();
            }
        }

        //set
        if (auth()->user()->pref_filter_own_projects == 'yes') {
            request()->merge(['filter_my_projects' => auth()->id()]);
        }

        //set
        if (auth()->user()->pref_filter_show_archived_projects == 'yes') {
            request()->merge(['filter_show_archived_projects' => 'yes']);
        }

    }

    /** -------------------------------------------------------------------------
     * Save the users current project viewing preference
     * @return null
     * -------------------------------------------------------------------------*/
    function toggleView() {

        if (request()->filled('toggle_project_view')) {

            //valid togle states
            $valid = [
                'list',
                'card',
                'milestone',
                'pipeline',
                'category',
                'gnatt', 
            ];

            //ensure its a valid toggle
            auth()->user()->pref_view_projects_layout = request('toggle_project_view');
            auth()->user()->save();
        }

    }
}
