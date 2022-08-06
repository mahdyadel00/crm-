<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [general] precheck processes for categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Categories;
use Closure;
use Log;

class General {

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

        //frontend
        $this->fronteEnd();

        return $next($request);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //page level javascript
        config(['js.section' => 'categories']);

    }

}
