<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Projects;

use Closure;

class ProjectTemplatesGeneral {


    /**
     * Inject any dependencies here
     *
     */
    public function __construct() {

    }

    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //set filter for project templates
        request()->merge([
            'filter_project_type' => 'template',
        ]);

        return $next($request);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

    }
}
