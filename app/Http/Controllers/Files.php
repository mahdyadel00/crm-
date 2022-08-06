<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Files\CreateResponse;
use App\Http\Responses\Files\DestroyResponse;
use App\Http\Responses\Files\IndexResponse;
use App\Http\Responses\Files\StoreResponse;
use App\Http\Responses\Files\UpdateResponse;
use App\Permissions\FilePermissions;
use App\Permissions\ProjectPermissions;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\FileRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Files extends Controller {

    /**
     * The file repository instance.
     */
    protected $filerepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The file permission instance.
     */
    protected $filepermissions;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The resources that are associated with files (e.g. project | lead | client)
     */
    protected $approved_resources;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    public function __construct(
        FileRepository $filerepo,
        TagRepository $tagrepo,
        ProjectPermissions $projectpermissions,
        UserRepository $userrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        FilePermissions $filepermissions
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('filesMiddlewareIndex')->only([
            'index',
            'store',
            'renameFile',
        ]);

        $this->middleware('filesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('filesMiddlewareDownload')->only([
            'download',
        ]);

        $this->middleware('filesMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->middleware('filesMiddlewareEdit')->only(
            [
                'edit',
                'renameFile',
            ]);

        $this->filerepo = $filerepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->filepermissions = $filepermissions;
        $this->projectpermissions = $projectpermissions;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;

        //allowable resource_types
        $this->approved_resources = [
            'project',
            'lead',
            'client',
        ];
    }

    /**
     * Display a listing of files
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $files = $this->filerepo->search();

        //apply some permissions
        if ($files) {
            foreach ($files as $file) {
                $this->applyPermissions($file);
            }
        }

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('file');

        //mark events as read
        if (request()->filled('fileresource_type') && request()->filled('fileresource_id')) {
            \App\Models\EventTracking::where('resource_id', request('fileresource_id'))
                ->where('resource_type', request('fileresource_type'))
                ->where('eventtracking_userid', auth()->id())
                ->update(['eventtracking_status' => 'read']);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('files'),
            'files' => $files,
            'tags' => $tags,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Additional settings for external requests
     */
    public function externalRequest() {

        //check we have a file id and type
        if (!is_numeric(request('project_id'))) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }
    }

    /**
     * Show the form for creating a new file.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //validate the incoming request
        if (!is_numeric(request('resource_id')) || !in_array(request('resource_type'), $this->approved_resources)) {
            //hide the add button
            request()->merge([
                'visibility_list_page_actions_add_button' => false,
            ]);
        }

        //payload
        $payload = [];

        //show the view
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created file in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //defaults
        $file_clientid = null;

        //validation
        if (!is_numeric(request('fileresource_id')) || !in_array(request('fileresource_type'), $this->approved_resources)) {
            //error
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //counting rows
        $rows = $this->filerepo->search();
        $count = $rows->total();

        //[save attachments] loop through and save each attachment
        $unique_key = Str::random(50);
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'file_clientid' => request('file_clientid'),
                    'fileresource_type' => request('fileresource_type'),
                    'fileresource_id' => request('fileresource_id'),
                    'file_directory' => $uniqueid,
                    'file_uniqueid' => $uniqueid,
                    'file_upload_unique_key' => $unique_key,
                    'file_filename' => $file_name,
                ];
                //process and save to db
                $file_id = $this->filerepo->process($data);
                //get file
                $files = $this->filerepo->search($file_id);
                $file = $files->first();
                //dd($file_id);

                //record event (project file)
                if (request('fileresource_type') == 'project' && request('fileresource_id') > 0) {
                    if ($project = \App\Models\Project::Where('project_id', request('fileresource_id'))->first()) {
                        /** ----------------------------------------------
                         * record event [status]
                         * ----------------------------------------------*/
                        $data = [
                            'event_creatorid' => auth()->id(),
                            'event_item' => 'file',
                            'event_item_id' => $file_id,
                            'event_item_lang' => 'event_uploaded_a_file',
                            'event_item_content' => $file_name,
                            'event_item_content2' => "files/download?file_id=$uniqueid",
                            'event_parent_type' => 'project',
                            'event_parent_id' => $project->project_id,
                            'event_parent_title' => $project->project_title,
                            'event_show_item' => 'yes',
                            'event_show_in_timeline' => $file->file_visibility_client,
                            'event_clientid' => $project->project_clientid,
                            'eventresource_type' => 'project',
                            'eventresource_id' => $project->project_id,
                            'event_notification_category' => 'notifications_projects_activity',
                            'event_client_visibility' => $file->file_visibility_client,
                        ];
                        //record event
                        if ($event_id = $this->eventrepo->create($data)) {
                            //get users
                            $users = $this->projectpermissions->check('users', $project);
                            //record notification
                            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
                        }

                        /** ----------------------------------------------
                         * send email [status]
                         * ----------------------------------------------*/
                        if (isset($emailusers) && is_array($emailusers)) {
                            //send to users
                            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                                foreach ($users as $user) {
                                    $mail = new \App\Mail\ProjectFileUploaded($user, $file, $project);
                                    $mail->build();
                                }
                            }
                        }

                    }
                }
            }
        }

        //get files (only those uploaded now)
        request()->merge([
            'filter_file_upload_unique_key' => $unique_key,
        ]);
        $files = $this->filerepo->search();

        foreach ($files as $file) {
            $this->applyPermissions($file);
        }

        $payload = [
            'page' => $this->pageSettings('files'),
            'files' => $files,
            'count' => $count,
        ];

        //show the view
        return new StoreResponse($payload);
    }

    /**
     * show the form to edit a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $file = \App\Models\File::Where('file_id', $id)->first();

        $extension = '.' . $file->file_extension;
        $payload['filename'] = str_replace($extension, '', $file->file_filename);

        //page
        $html = view('pages/files/components/actions/rename', compact('payload'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#actionsModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        $jsondata['dom_visibility'][] = [
            'selector' => '#actionsModalFooter', 'action' => 'show',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * show the form to create a new resource
     *
     * @return \Illuminate\Http\Response
     */
    public function renameFile($id) {

        //get the item
        $file = \App\Models\File::Where('file_id', $id)->first();

        if (!request()->filled('file_filename')) {
            abort(409, __('lang.file_name') . ' - ' . __('lang.is_required'));
        }

        //new filename
        $new_filename = request('file_filename') . '.' . $file->file_extension;

        //rename file
        try {
            $old_file = BASE_DIR . '/storage/files/' . $file->file_directory . '/' . $file->file_filename;
            $new_file = BASE_DIR . '/storage/files/' . $file->file_directory . '/' . $new_filename;
            rename($old_file, $new_file);
        } catch (Exception $e) {
            $message = $e->getMessage();
            $error_code = $e->getCode();
            if ($error_code = 123) {
                abort(409, __('lang.invalid_file_name'));
            }
            abort(409, $message);
        }

        //update db
        $file->file_filename = $new_filename;
        $file->save();

        //get friendly row
        $files = $this->filerepo->search($id);

        //apply some permissions
        if ($files) {
            foreach ($files as $file) {
                $this->applyPermissions($file);
            }
        }

        //payload
        $payload = [
            'files' => $files,
            'file' => $files->first(),
        ];

        //return view
        return new UpdateResponse($payload);

    }

    /**
     * show file image thumbs in tables
     * @return \Illuminate\Http\Response
     */
    public function showImage() {

        $image = '';

        //default image
        $default_image = 'system/images/image-placeholder.jpg';
        if (Storage::exists($default_image)) {
            $image = $default_image;
        }

        //check if file exists in the database

        //get the file from database
        if (request()->filled('file_id')) {
            if ($file = \App\Models\File::Where('file_uniqueid', request('file_id'))->first()) {
                //confirm thumb exists
                if ($file->file_thumbname != '') {
                    $image_path = "files/$file->file_directory/$file->file_thumbname";
                    if (Storage::exists($image_path)) {
                        $image = $image_path;
                    }
                }
            }
        }

        //browser image response
        if ($image != '') {
            $thumb = Storage::get($image);
            $mime = Storage::mimeType($image);
            header('Content-Type: image/gif');
            echo $thumb;
        }
    }

    /**
     * download a file
     * @return \Illuminate\Http\Response
     */
    public function download() {

        //get the file
        $file = \App\Models\File::Where('file_uniqueid', request('file_id'))->first();

        //mark events as read
        \App\Models\EventTracking::where('eventtracking_source_id', $file->file_id)
            ->where('eventtracking_source', 'file')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //confirm file physaiclly exists
        if ($file->file_filename != '') {
            $file_path = "files/$file->file_directory/$file->file_filename";
            if (Storage::exists($file_path)) {
                return Storage::download($file_path);
            }
        }

        //error item not found
        abort(404, __('lang.file_not_found'));
    }

    /**
     * Update the specified file in storage.
     * @param int $id file id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //update file
        if (!$this->filerepo->update($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //update file timeline visibility
        if ($event = \App\Models\Event::Where('event_item', 'file')->Where('event_item_id', $id)->first()) {
            $event->event_show_in_timeline = (request('visible_to_client') == 'on') ? 'yes' : 'no';
            $event->save();
        }

        //success notification - no real need to show a confirmation for this
        //return response()->json(success());
    }

    /**
     * Remove the specified file from storage.
     * @param object DestroyRepository instance of the repository
     * @param int $id file id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo, $id) {

        //delete file
        $destroyrepo->destroyFile($id);

        //reponse payload
        $payload = [
            'file_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
    }

    /**
     * pass the file through the ProjectPermissions class and apply user permissions.
     * @param object $file file model
     * @return object
     */
    private function applyPermissions($file = '') {

        //sanity - make sure this is a valid file object
        if ($file instanceof \App\Models\File) {
            //project files
            if ($file->fileresource_id > 0) {
                $file->permission_delete_file = $this->filepermissions->check('delete', $file);
                $file->permission_edit_file = $this->filepermissions->check('edit', $file);
            }
            //template files
            if ($file->fileresource_id < 0) {
                $file->permission_delete_file = (auth()->user()->role->role_templates_projects >= 2) ? true : false;
                $file->permission_edit_file = (auth()->user()->role->role_templates_projects >= 2) ? true : false;
            }
        }
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.files'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'files',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_files' => 'active',
            'sidepanel_id' => 'sidepanel-filter-files',
            'dynamic_search_url' => url('files/search?action=search&fileresource_id=' . request('fileresource_id') . '&fileresource_type=' . request('fileresource_type')),
            'add_button_classes' => 'add-edit-file-button',
            'load_more_button_route' => 'files',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_file'),
            'add_modal_create_url' => url('files/create?fileresource_id=' . request('fileresource_id') . '&fileresource_type=' . request('fileresource_type')),
            'add_modal_action_url' => url('files?fileresource_id=' . request('fileresource_id') . '&fileresource_type=' . request('fileresource_type')),
            'add_modal_action_ajax_class' => 'js-ajax-ux-request',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //files list page
        if ($section == 'files') {
            $page += [
                'meta_title' => __('lang.files'),
                'heading' => __('lang.files'),
                'sidepanel_id' => 'sidepanel-filter-files',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
        }

        //return
        return $page;
    }
}