<?php

namespace App\Permissions;

use App\Repositories\LeadRepository;
use Illuminate\Support\Facades\Log;

class LeadPermissions {

    /**
     * The repository instance.
     */
    protected $leadrepo;

    /**
     * Inject dependecies
     */
    public function __construct(LeadRepository $leadrepo) {
        $this->leadrepo = $leadrepo;
    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     */
    public function permissionChecksArray() {
        $checks = [
            'view',
            'edit',
            'delete',
            'participate',
            'users',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified lead ONLY.
     *     - The permission levels derived here can also be applied to ancillary lead items (see below):
     *                      - comments
     *                      - files
     *
     * [EXAMPLE USAGE]
     *          if (!$this->leadpermissons->check('delete', $lead->lead_id)) {
     *                 abort(413)
     *          }
     *
     * @param numeric $resource id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @param mixed $lead can be the lead id or the actual lead object. [IMPORTANT]: passed lead object must from leadrepo->search()
     * @return bool true if user has permission
     */
    public function check($action = '', $lead = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($lead)) {
            if (!$lead = \App\Models\Lead::Where('lead_id', $lead)->first()) {
                Log::error("the lead coud not be found", ['process' => '[permissions][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'lead_id' => $lead ?? '']);
            }
        }

        //[IMPORTANT]: any passed lead object must from leadrepo->search() method, not the lead model
        if ($lead instanceof \App\Models\Lead || $lead instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //array of assigned users
            $assigned_users = $lead->assigned->pluck('id');
        } else {
            Log::error("the lead coud not be found", ['process' => '[permissions][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        /**
         * [ARRAY OF USERS (with view level permssions)]
         * [NOTES] this must have the same logic as $action == 'view' below
         */
        if ($action == 'users') {
            $list = [];
            $users = \App\Models\User::with('role')->get();
            foreach ($users as $user) {
                if ($user->id > 0) {
                    //global user
                    if ($user->role->role_leads >= 1 && $user->role->role_leads_scope == 'global') {
                        $list[] = $user->id;
                        continue;
                    }
                    //assigned
                    if ($assigned_users->contains($user->id)) {
                        $list[] = $user->id;
                        continue;
                    }
                }
            }
            return $list;
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
         * [CLIENT]
         * Deny all clients
         *
         */
        if (auth()->user()->is_client) {
            return false;
        }

        /**
         * [VIEW A LEAD]
         */
        if ($action == 'view') {
            //global user
            if (auth()->user()->role->role_leads >= 1 && auth()->user()->role->role_leads_scope == 'global') {
                return true;
            }
            //assigned
            if ($assigned_users->contains(auth()->id())) {
                return true;
            }
        }

        /**
         * [EDITING A LEAD]
         */
        if ($action == 'edit') {
            //global user
            if (auth()->user()->role->role_leads >= 2 && auth()->user()->role->role_leads_scope == 'global') {
                return true;
            }
            //assigned
            if ($assigned_users->contains(auth()->id())) {
                return true;
            }
            //creator
            if ($lead->lead_creatorid == auth()->id()) {
                return true;
            }
        }

        /**
         * [PARTICPATE]
         * - comments, attach files, create checklists, etc
         */
        if ($action == 'participate') {
            //assigned
            if ($assigned_users->contains(auth()->id())) {
                return true;
            }

            //user with global access
            if (auth()->user()->role->role_leads_scope == 'global' && auth()->user()->role->role_leads >= 2) {
                return true;
            }
        }

        /**
         * [DELETING A LEAD]
         */
        if ($action == 'delete') {
            //global user
            if (auth()->user()->role->role_leads >= 3 && auth()->user()->role->role_leads_scope == 'global') {
                return true;
            }
            //creator
            if ($lead->lead_creatorid == auth()->id()) {
                return true;
            }
        }

        //passed
        Log::info("user does not have the requested permission level ($action) for this lead", ['process' => '[permissions][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
        return false;
    }

}