<?php

namespace App\Permissions;

use App\Permissions\LeadPermissions;
use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use App\Repositories\ChecklistRepository;
use Illuminate\Support\Facades\Log;

class ChecklistPermissions {

    /**
     * The checklist repository instance.
     */
    protected $checklistrepo;

    /**
     * The project permissions instance.
     */
    protected $projectpermissons;

    /**
     * The project permissions instance.
     */
    protected $leadpermissons;

    /**
     * The task permissions instance.
     */
    protected $taskpermissons;

    /**
     * Inject dependecies
     */
    public function __construct(
        ChecklistRepository $checklistrepo,
        ProjectPermissions $projectpermissons,
        LeadPermissions $leadpermissons,
        TaskPermissions $taskpermissons
    ) {

        $this->checklistrepo = $checklistrepo;
        $this->projectpermissons = $projectpermissons;
        $this->taskpermissons = $taskpermissons;
        $this->leadpermissons = $leadpermissons;

    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     * @return array
     */
    public function permissionChecksArray() {
        $checks = [
            'edit-delete',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified Checklist ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->checklistpermissons->check($checklist_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $checklist object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @return bool true if user has permission
     */
    public function check($action = '', $checklist = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][checklist]', config('app.debug_ref'), 'function' => __function__, 'checklist' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($checklist)) {
            if (!$checklist = \App\Models\Checklist::Where('checklist_id', $checklist)->first()) {
                Log::error("the checklist coud not be found", ['process' => '[permissions][checklist]', config('app.debug_ref'), 'function' => __function__, 'checklist' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed checklist object must from checklistrepo->search() method, not the checklist model
        if ($checklist instanceof \App\Models\Checklist || $checklist instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //all is ok
        } else {
            Log::error("the checklist coud not be found", ['process' => '[permissions][checklist]', config('app.debug_ref'), 'function' => __function__, 'checklist' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        /**
         * [ADMIN]
         * Grant full permission for whatever request
         *
         */
        if (auth()->user()->role_id == 1) {
            return true;
        }

        /**
         * [DELETE COMMENTS]
         */
        if ($action == 'edit-delete') {
            //task checklists
            if ($checklist->checklistresource_type == 'task') {
                if ($this->taskpermissons->check('edit', $checklist->checklistresource_id)) {
                    return true;
                }
            }

            //lead checklists
            if ($checklist->checklistresource_type == 'lead') {
                if ($this->leadpermissons->check('edit', $checklist->checklistresource_id)) {
                    return true;
                }
            }

        }

        //failed
        Log::info("permissions denied on this checklist", ['process' => '[permissions][checklists]', config('app.debug_ref'), 'function' => __function__, 'checklist' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}