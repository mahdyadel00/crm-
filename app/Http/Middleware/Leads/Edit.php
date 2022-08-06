<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;

use App\Models\Lead;
use App\Permissions\LeadPermissions;
use Closure;
use Log;

class Edit {

    /**
     * The permisson repository instance.
     */
    protected $leadpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(LeadPermissions $leadpermissions, Lead $lead_model) {

        //lead permissions repo
        $this->leadpermissions = $leadpermissions;

    }

    /**
     * This middleware does the following
     *   1. validates that the lead exists
     *   2. checks users permissions to [edit] the resource
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //validate module status
        if (!config('visibility.modules.leads')) {
            abort(404, __('lang.the_requested_service_not_found'));
            return $next($request);
        }

        //lead id
        $lead_id = $request->route('lead');

        //frontend
        $this->fronteEnd();

        //basic validation
        if (!$lead = \App\Models\Lead::Where('lead_id', $lead_id)->first()) {
            Log::error("lead could not be found", ['process' => '[permissions][leads][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
            abort(409, __('lang.lead_not_found'));
        }

        //permission: does user have permission to edit this lead
        if ($this->leadpermissions->check('edit', $lead_id)) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][leads][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

        //assigning a lead and setting its manager
        if (auth()->user()->role->role_assign_leads == 'yes') {
            config(['visibility.lead_modal_assign_fields' => true]);
            request()->merge([
                'edit_assigned' => true,
            ]);
        }
    }
}
