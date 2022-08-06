<?php

namespace App\Permissions;

use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use App\Permissions\LeadPermissions;
use App\Repositories\AttachmentRepository;
use Illuminate\Support\Facades\Log;

class AttachmentPermissions {

    /**
     * The attachment repository instance.
     */
    protected $attachmentrepo;

    /**
     * The project permissions instance.
     */
    protected $projectpermissons;

    /**
     * The task permissions instance.
     */
    protected $taskpermissons;

    /**
     * The lead permissions instance.
     */
    protected $leadpermissons;

    /**
     * Inject dependecies
     */
    public function __construct(
        AttachmentRepository $attachmentrepo,
        ProjectPermissions $projectpermissons,
        TaskPermissions $taskpermissons,
        LeadPermissions $leadpermissons
    ) {

        $this->attachmentrepo = $attachmentrepo;
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
            'delete',
            'download',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified Attachment ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->attachmentpermissons->check($attachment_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $attachment object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @return bool true if user has permission
     */
    public function check($action = '', $attachment = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][attachment]', config('app.debug_ref'), 'function' => __function__, 'attachment' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($attachment)) {
            if (!$attachment = \App\Models\Attachment::Where('attachment_id', $attachment)->first()) {
                Log::error("the attachment coud not be found", ['process' => '[permissions][attachment]', config('app.debug_ref'), 'function' => __function__, 'attachment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed attachment object must from attachmentrepo->search() method, not the attachment model
        if ($attachment instanceof \App\Models\Attachment || $attachment instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //all is ok
        } else {
            Log::error("the attachment coud not be found", ['process' => '[permissions][attachment]', config('app.debug_ref'), 'function' => __function__, 'attachment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if ($action == 'delete') {
            //user who created or admin user
            if ($attachment->attachment_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                return true;
            }

            //project attachments
            if ($attachment->attachmentresource_type == 'project') {
                //an admin lever user of the project
                if ($this->projectpermissons->check('super-user', $attachment->attachmentresource_id)) {
                    return true;
                }
            }

            //task attachments
            if ($attachment->attachmentresource_type == 'task') {
                //an admin lever user of the project
                if ($this->taskpermissons->check('super-user', $attachment->attachmentresource_id)) {
                    return true;
                }
            }

            //lead attachments
            //only attachment creator and admin (as checked above)
        }

        /**
         * [DELETE COMMENTS]
         */
        if ($action == 'download') {
            //user who created or admin user
            if ($attachment->attachment_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                return true;
            }

            //project attachments
            if ($attachment->attachmentresource_type == 'project') {
                //user who can view the project
                if ($this->projectpermissons->check('view', $attachment->attachmentresource_id)) {
                    return true;
                }
            }

            //task attachments
            if ($attachment->attachmentresource_type == 'task') {
                //user who can view the task
                if ($this->taskpermissons->check('view', $attachment->attachmentresource_id)) {
                    return true;
                }
            }

            //lead attachments
            if ($attachment->attachmentresource_type == 'lead') {
                //user wh can view the lead
                if ($this->leadpermissons->check('view', $attachment->attachmentresource_id)) {
                    return true;
                }
            }
        }

        //failed
        Log::info("permissions denied on this attachment", ['process' => '[permissions][attachments]', config('app.debug_ref'), 'function' => __function__, 'attachment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}