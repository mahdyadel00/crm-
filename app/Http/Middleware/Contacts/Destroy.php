<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for contacts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Contacts;
use Closure;
use Log;

class Destroy {

    /**
     * This 'bulk actions' middleware does the following
     *   1. If the request was for a sinle item
     *         - single item actions must have a query string '?id=123'
     *         - this id will be merged into the expected 'ids' request array (just as if it was a bulk request)
     *   2. loop through all the 'ids' that are in the post request
     *
     * HTML for the checkbox is expected to be in this format:
     *   <input type="checkbox" name="ids[{{ $contact->contact_id }}]"
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //NOTE: F4 change [action-bar] to the action name e.g. [change-status]

        //for a single item request - merge into an $ids[x] array and set as if checkox is selected (on)
        if (is_numeric($request->route('contact'))) {
            $ids[$request->route('contact')] = 'on';
            request()->merge([
                'ids' => $ids,
            ]);
        }

        //loop through each contact and check permissions
        if (is_array(request('ids'))) {

            //validate each item in the list exists
            foreach (request('ids') as $id => $value) {
                //only checked items
                if ($value == 'on') {
                    //validate
                    if (!$contact = \App\Models\User::Where('id', $id)->first()) {
                        abort(409, __('lang.one_of_the_selected_items_nolonger_exists'));
                    }
                    //cannot delete account owner
                    if($contact->account_owner =='yes'){
                        abort(409, __('lang.you_cannot_delete_account_owner'));
                    }
                    //client
                    if (auth()->user()->is_client) {
                        if ($contact->clientid != auth()->user()->clientid) {
                            Log::error("permission denied", ['process' => '[permissions][contacts]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
                            abort(403);
                        }
                    }
                }
            }

            //permission: does user have permission edit contacts
            if (auth()->user()->is_team) {
                if (auth()->user()->role->role_contacts < 3) {
                    Log::error("permission denied", ['process' => '[permissions][contacts]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'user_id' => auth()->id()]);
                    abort(403, __('lang.permission_denied_for_this_item') . " - #$id");
                }
            }
        } else {
            //no items were passed with this request
            Log::error("no items were sent with this request", ['process' => '[permissions][contacts]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'contact id' => $contact_id ?? '']);
            abort(409);
        }

        //all is on - passed
        return $next($request);
    }
}
