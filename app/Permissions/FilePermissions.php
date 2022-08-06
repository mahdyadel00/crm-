<?php

namespace App\Permissions;

use App\Permissions\ProjectPermissions;
use App\Repositories\FileRepository;
use Illuminate\Support\Facades\Log;

class FilePermissions {

    /**
     * The file repository instance.
     */
    protected $filerepo;

    /**
     * The project permissions instance.
     */
    protected $projectpermissons;

    /**
     * Inject dependecies
     */
    public function __construct(
        FileRepository $filerepo,
        ProjectPermissions $projectpermissons
    ) {

        $this->filerepo = $filerepo;
        $this->projectpermissons = $projectpermissons;

    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     * @return array
     */
    public function permissionChecksArray() {
        $checks = [
            'download',
            'delete',
            'edit',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified File ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->filepermissons->check($file_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $file object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @return bool true if user has permission
     */
    public function check($action = '', $file = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][file]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($file)) {
            if (!$file = \App\Models\File::Where('file_id', $file)->first()) {
                Log::error("the file coud not be found", ['process' => '[permissions][file]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed file object must from filerepo->search() method, not the file model
        if ($file instanceof \App\Models\File || $file instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //all is ok
        } else {
            Log::error("the file coud not be found", ['process' => '[permissions][file]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
         * [DOWNLOAD FILES]
         */
        if ($action == 'download') {
            //project file
            if ($file->fileresource_type == 'project') {
                //user who can view can also dowload
                if ($this->projectpermissons->check('files-view', $file->fileresource_id)) {
                    return true;
                }
            }
            //client files
            if ($file->fileresource_type == 'client') {
                if (auth()->user()->is_team) {
                    if (auth()->user()->role_id == 1 || auth()->user()->role_clients <= 2) {
                        return true;
                    }
                }
            }
        }

        /**
         * [DELETE FILES]
         */
        if ($action == 'delete') {
            //project file
            if ($file->fileresource_type == 'project') {
                //creator
                if ($file->file_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                    return true;
                }
                //an admin lever user of the project
                if ($this->projectpermissons->check('super-user', $file->fileresource_id)) {
                    return true;
                }
            }
            //client files
            if ($file->fileresource_type == 'client') {
                if (auth()->user()->is_team) {
                    if (auth()->user()->role_id == 1 || auth()->user()->role_clients <= 2) {
                        return true;
                    }
                }
            }
        }

        /**
         * [EDIT FILES]
         */
        if ($action == 'edit') {
            //project file
            if ($file->fileresource_type == 'project' || $file->fileresource_type == 'source-billing') {
                //creator
                if ($file->file_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                    return true;
                }
                //client - do not allow client to delete files (even though they created the [sourcing request] and so would pass as a supoer-user,below)
                if (auth()->user()->is_client) {
                    if ($file->file_clientid != auth()->user()->clientid) {
                        return false;
                    }
                }
                //an admin lever user of the project
                if ($this->projectpermissons->check('super-user', $file->fileresource_id)) {
                    return true;
                }
            }

            if ($file->fileresource_type == 'client') {
                if (auth()->user()->is_team) {
                    if (auth()->user()->role_id == 1 || auth()->user()->role_clients <= 2) {
                        return true;
                    }
                }
            }
        }

        //failed
        Log::info("permissions denied on this file", ['process' => '[permissions][files]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}