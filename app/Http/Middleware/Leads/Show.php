<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [show] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;

use App\Models\Lead;
use App\Permissions\LeadPermissions;
use App\Repositories\LeadRepository;
use Closure;
use Log;

class Show {

    //vars
    protected $leadpermissions;
    protected $leadmodel;
    protected $leadrepo;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(LeadPermissions $leadpermissions, Lead $leadmodel, LeadRepository $leadrepo) {

        $this->leadpermissions = $leadpermissions;
        $this->leadmodel = $leadmodel;
        $this->leadrepo = $leadrepo;

    }

    /**
     * This middleware does the following:
     *   1. validates that the lead exists
     *   2. checks users permissions to [show] the resource
     *   3. sets various visibility and permissions settings (e.g. menu items, edit buttons etc)
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

        //basic validation
        if (!$lead = $this->leadmodel::find($lead_id)) {
            Log::error("lead could not be found", ['process' => '[permissions][leads][destroy]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
            abort(404);
        }

        //friendly format
        $leads = $this->leadrepo->search($lead_id);
        $lead = $leads->first();

        //frontend
        $this->fronteEnd($lead);

        //permission: does user have permission to view this lead
        if ($this->leadpermissions->check('view', $lead)) {

            //mark notifications as read
            \App\Models\EventTracking::where('resource_type', 'lead')
                ->where('resource_id', $lead_id)
                ->where('eventtracking_userid', auth()->id())
                ->update(['eventtracking_status' => 'read']);

            //permission granted
            return $next($request);
        }

        Log::error("permission denied", ['process' => '[permissions][leads][show]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd($lead = '') {

        //all users
        if ($this->leadpermissions->check('view', $lead)) {
            config([
                'settings.lead_permissions_participate' => $this->leadpermissions->check('particiate', $lead),
            ]);
        }

        if (auth()->user()->is_team) {
            if ($this->leadpermissions->check('edit', $lead)) {
                config([
                    'visibility.lead_edit_button' => true,
                    'visibility.lead_editing_buttons' => true,
                ]);
            }
        }
    }

}
