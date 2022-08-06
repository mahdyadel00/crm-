<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [create] precheck processes for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Leads;
use App\Permissions\ChecklistPermissions;
use Closure;
use Log;

class EditDeleteChecklist {

    /**
     * The permisson repository instance.
     */
    protected $checklistpermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(ChecklistPermissions $checklistpermissions) {

        //permissions
        $this->checklistpermissions = $checklistpermissions;

    }

    /**
     * Check user permissions to edit a lead
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

        //attachement id
        $checklist_id = $request->route('checklistid');

        //does the lead exist
        if (!$checklist = \App\Models\Checklist::Where('checklist_id', $checklist_id)->first()) {
            Log::error("checklist could not be found", ['process' => '[permissions][leads][delete-checklist]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'checklist id' => $checklist_id ?? '']);

            //error - remove item with message
            $jsondata['dom_visibility'][] = array(
                'selector' => "#lead_checklist_container_$checklist_id",
                'action' => 'slideup-slow-remove',
            );
            $jsondata['notification'] = [
                'type' => 'force-error',
                'value' => __('lang.item_nolonger_exists_or_removed'),
            ];
            return response()->json($jsondata);
        }

        //check permissions
        if ($checklist->checklistresource_type == 'lead') {
            if ($this->checklistpermissions->check('edit-delete', $checklist_id)) {
                return $next($request);
            }
        }

        //no items were passed with this request
        Log::error("permission denied", ['process' => '[permissions][leads][delete-checklist]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'checklist id' => $checklist_id ?? '']);
        abort(403);
    }

    private function removeItem($checklist_id) {

        $jsondata['dom_visibility'][] = array(
            'selector' => "#lead_checklist_container_$checklistid",
            'action' => 'slideup-slow-remove',
        );
        //response
        return response()->json($jsondata);
    }
}