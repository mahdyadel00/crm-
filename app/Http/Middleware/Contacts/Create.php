<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for contacts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Contacts;
use Closure;
use Log;

class Create {

    /**
     * This middleware does the following
     *   2. checks users permisions to [view] contacts
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //frontend
        $this->fronteEnd();

        //permision: does user have permision create contacts
        if (auth()->user()->role->role_contacts >= 2) {
            return $next($request);
        }

        //client user
        if (auth()->user()->is_client) {
            if (auth()->user()->account_owner == 'yes') {
                request()->merge([
                    'clientid' => auth()->user()->clientid,
                ]);
                return $next($request);
            }
        }

        //permision denied
        Log::error("permision denied", ['proces' => '[permisions][contacts][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        /**
         * [embedded request]
         * the add new contacts request is being made from an embedded view (project page)
         *      - validate the project
         *      - do no display 'project' & 'client' options in the modal form
         *  */
        if (request()->filled('contactresource_id') && request()->filled('contactresource_type')) {

            //client resource
            if (request('contactresource_type') == 'client') {
                if ($client = \App\Models\Client::Where('client_id', request('contactresource_id'))->first()) {

                    //hide some form fields
                    config([
                        'settings.visibility_contacts_modal_client_fields' => false,
                    ]);

                    //required form data
                    request()->merge([
                        'clientid' => $client->client_id,
                    ]);

                } else {
                    //error not found
                    Log::error("the resource project could not be found", ['proces' => '[permisions][contacts][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    abort(404);
                }
            }
        }

        //team user
        if (auth()->user()->is_team) {
            config([
                'settings.visibility_contacts_modal_account_owner' => true,
                'settings.visibility_contacts_modal_client_fields' => true,
            ]);
            if (request('contactresource_type') != 'client') {
                config([
                    'visibility.contacts_modal_client_fields' => true,
                ]);
            }
        }
    }
}
