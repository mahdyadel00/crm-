<?php

namespace App\Permissions;

use App\Permissions\ProjectPermissions;
use App\Repositories\NoteRepository;
use Illuminate\Support\Facades\Log;

class NotePermissions {

    /**
     * The note repository instance.
     */
    protected $noterepo;

    /**
     * The project permissions instance.
     */
    protected $projectpermissons;

    /**
     * Inject dependecies
     */
    public function __construct(
        NoteRepository $noterepo,
        ProjectPermissions $projectpermissons
    ) {

        $this->noterepo = $noterepo;
        $this->projectpermissons = $projectpermissons;

    }

    /**
     * The array of checks that are available.
     * NOTE: when a new check is added, you must also add it to this array
     * @return array
     */
    public function permissionChecksArray() {
        $checks = [
            'edit-delete',
            'show',
        ];
        return $checks;
    }

    /**
     * This method checks a users permissions for a particular, specified File ONLY.
     *
     * [EXAMPLE USAGE]
     *          if (!$this->notepermissons->check($note_id, 'delete')) {
     *                 abort(413)
     *          }
     *
     * @param numeric $note object or id of the resource
     * @param string $action [required] intended action on the resource se list above
     * @return bool true if user has permission
     */
    public function check($action = '', $note = '') {

        //VALIDATIOn
        if (!in_array($action, $this->permissionChecksArray())) {
            Log::error("the requested check is invalid", ['process' => '[permissions][note]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'check' => $action ?? '']);
            return false;
        }

        //GET THE RESOURCE
        if (is_numeric($note)) {
            if (!$note = \App\Models\Note::Where('note_id', $note)->first()) {
                Log::error("the note coud not be found", ['process' => '[permissions][note]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            }
        }

        //[IMPORTANT]: any passed file object must from filerepo->search() method, not the file model
        if ($note instanceof \App\Models\Note || $note instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            //all is ok
        } else {
            Log::error("the note coud not be found", ['process' => '[permissions][note]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        /**
         * [DOWNLOAD FILES]
         */
        if ($action == 'view') {

            //creator
            if ($note->note_creatorid == auth()->id()) {
                return true;
            }

            //public notes
            if ($note->note_visibility == 'public') {
                //project note
                if ($note->noteresource_type == 'project') {
                    //user who can view can also dowload
                    if ($this->projectpermissons->check('notes-view', $note->noteresource_id)) {
                        return true;
                    }
                }

                //client note
                if ($note->noteresource_type == 'client') {
                    if (auth()->user()->role->role_clients >= 1) {
                        return true;
                    }
                }
            }
        }

        /**
         * [EDIT OR DELETE NOTE]
         */
        if ($action == 'edit-delete') {
            //creator
            if ($note->note_creatorid == auth()->id() || auth()->user()->role_id == 1) {
                return true;
            }

            //project note
            if ($note->noteresource_type == 'project') {
                if ($this->projectpermissons->check('super-user', $note->noteresource_id)) {
                    return true;
                }
            }
            //client note
            if ($note->noteresource_type == 'client') {
                if (auth()->user()->role->role_clients >= 2) {
                    return true;
                }
            }
        }

        //failed
        Log::info("permissions denied on this note", ['process' => '[permissions][files]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}