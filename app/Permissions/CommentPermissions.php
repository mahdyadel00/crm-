<?php

namespace App\Permissions;

use App\Permissions\ProjectPermissions;
use App\Permissions\TaskPermissions;
use App\Repositories\CommentRepository;
use Illuminate\Support\Facades\Log;

class CommentPermissions {

    /**
     * The comment repository instance.
     */
    protected $commentrepo;

    /**
     * The project permissions instance.
     */
    protected $projectpermissons;

    /**
     * The task permissions instance.
     */
    protected $taskpermissons;


    /**
     * Inject dependecies
     */
    public function __construct(
        CommentRepository $commentrepo,
        ProjectPermissions $projectpermissons,
        TaskPermissions $taskpermissons
    ) {

        $this->commentrepo = $commentrepo;
        $this->projectpermissons = $projectpermissons;
        $this->taskpermissons = $taskpermissons;

    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     * @return array
     */
    public function permissionChecksArray() {
        $checks = [
            'delete',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified Comment ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->commentpermissons->check($comment_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $comment object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @return bool true if user has permission
     */
    public function check($action = '', $comment = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][comment]', config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($comment)) {
            if (!$comment = \App\Models\Comment::Where('comment_id', $comment)->first()) {
                Log::error("the comment coud not be found", ['process' => '[permissions][comment]', config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed comment object must from commentrepo->search() method, not the comment model
        if ($comment instanceof \App\Models\Comment || $comment instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //all is ok
        } else {
            Log::error("the comment coud not be found", ['process' => '[permissions][comment]', config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
            //user who posted or admin user
            if ($comment->comment_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                return true;
            }
            //project comments
            if ($comment->commentresource_type == 'project') {
                //an admin lever user of the project
                if ($this->projectpermissons->check('super-user', $comment->commentresource_id)) {
                    return true;
                }
            }
            //task comments
            if ($comment->commentresource_type == 'task') {
                //an admin lever user of the project
                if ($this->taskpermissons->check('super-user', $comment->commentresource_id)) {
                    return true;
                }
            }

            //lead comments
            //only comment creator and admin (as checked above)


        }

        //failed
        Log::info("permissions denied on this comment", ['process' => '[permissions][comments]', config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}