<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;
use App\Models\Lead;
use App\Permissions\LeadPermissions;
use App\Repositories\ProjectRepository;
use Closure;
use Log;

class Cloning {

    /**
     * The permisson repository instance.
     */
    protected $leadpermissions;
    protected $projectrepo;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(LeadPermissions $leadpermissions) {

        //lead permissions repo
        $this->leadpermissions = $leadpermissions;

    }

    /**
     * Check user permissions to edit a lead
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //lead id
        $lead_id = $request->route('lead');

        //does the lead exist
        if ($lead_id == '' || !$lead = \App\Models\Lead::Where('lead_id', $lead_id)->first()) {
            Log::error("lead could not be found", ['process' => '[permissions][leads][cloning]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
            abort(409, __('lang.lead_not_found'));
        }


        //permission for team members only (for now)
        if (auth()->user()->is_team) {
            if ($this->leadpermissions->check('view', $lead_id)) {
                return $next($request);
            }
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][leads][cloning]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead id' => $lead_id ?? '']);
        abort(403);
    }
}
