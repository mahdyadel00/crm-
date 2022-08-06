<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for comments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Comments\DestroyResponse;
use App\Http\Responses\Comments\IndexResponse;
use App\Http\Responses\Comments\StoreResponse;
use App\Permissions\CommentPermissions;
use App\Permissions\ProjectPermissions;
use App\Repositories\CommentRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class Comments extends Controller {

    /**
     * The comment repository instance.
     */
    protected $commentrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    /**
     * The comment permission instance.
     */
    protected $commentpermissions;

    /**
     * The project permission instance.
     */
    protected $projectpermissions;

    /**
     * The resources that are associated with comments (e.g. project | lead | client)
     */
    protected $approved_resources;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    public function __construct(
        CommentRepository $commentrepo,
        UserRepository $userrepo,
        ProjectRepository $projectrepo,
        ProjectPermissions $projectpermissions,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        CommentPermissions $commentpermissions
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('commentsMiddlewareIndex')->only([
            'index',
            'store',
        ]);

        $this->middleware('commentsMiddlewareCreate')->only([
            'store',
        ]);

        $this->middleware('commentsMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->commentrepo = $commentrepo;
        $this->userrepo = $userrepo;
        $this->eventrepo = $eventrepo;
        $this->commentpermissions = $commentpermissions;
        $this->projectpermissions = $projectpermissions;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->projectrepo = $projectrepo;

        //allowable resource_types
        $this->approved_resources = [
            'project',
            'lead',
            'client',
        ];
    }

    /**
     * Display a listing of comments
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $comments = $this->commentrepo->search();

        //apply some permissions
        if ($comments) {
            foreach ($comments as $comment) {
                $this->applyPermissions($comment);
            }
        }

        //mark events as read
        if (request()->filled('commentresource_type') && request()->filled('commentresource_id')) {
            \App\Models\EventTracking::where('resource_id', request('commentresource_id'))
                ->where('resource_type', request('commentresource_type'))
                ->where('eventtracking_userid', auth()->id())
                ->update(['eventtracking_status' => 'read']);
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('comments'),
            'comments' => $comments,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Store a newly created comment in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //basic page settings
        $page = $this->pageSettings('comments');

        //validate
        $validator = Validator::make(request()->all(), [
            'comment_text' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //create the item
        if (!$comment_id = $this->commentrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get comments
        $comments = $this->commentrepo->search($comment_id);
        $comment = $comments->first();
        $this->applyPermissions($comment);

        //record event (project comments)
        if (request('commentresource_type') == 'project') {
            $projects = $this->projectrepo->search(request('commentresource_id'));
            if ($project = $projects->first()) {
                /** ----------------------------------------------
                 * record event [status]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => auth()->id(),
                    'event_item' => 'comment',
                    'event_item_id' => $comment->comment_id,
                    'event_item_lang' => 'event_posted_a_comment',
                    'event_item_content' => $comment->comment_text,
                    'event_item_content2' => '',
                    'event_parent_type' => 'project',
                    'event_parent_id' => $project->project_id,
                    'event_parent_title' => $project->project_title,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'event_clientid' => $project->project_clientid,
                    'eventresource_type' => 'project',
                    'eventresource_id' => $project->project_id,
                    'event_notification_category' => 'notifications_projects_activity',
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
                    //the comment
                    $data = $comment->toArray();
                    //send to users
                    if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                        foreach ($users as $user) {
                            $mail = new \App\Mail\ProjectComment($user, $data, $project);
                            $mail->build();
                        }
                    }
                }
            }
        }

        $payload = [
            'page' => $page,
            'comments' => $comments,
        ];

        //show the view
        return new StoreResponse($payload);
    }

    /**
     * Remove the specified comment from storage.
     * @param object DestroyRepository instance of the repository
     * @param int $id comment id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo, $id) {

        //delete comment
        $destroyrepo->destroyComment($id);

        //reponse payload
        $payload = [
            'comment_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
    }

    /**
     * pass the comment through the ProjectPermissions class and apply user permissions.
     * @param object $comment
     * @return object
     */
    private function applyPermissions($comment = '') {

        //sanity - make sure this is a valid comment object
        if ($comment instanceof \App\Models\Comment) {
            //delete permissions
            $comment->permission_delete_comment = $this->commentpermissions->check('delete', $comment);
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
                __('lang.comments'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'comments',
            'no_results_message' => __('lang.no_results_found'),
            'load_more_button_route' => 'comments',
            'source' => 'list',
        ];

        //comments list page
        if ($section == 'comments') {
            $page += [
                'meta_title' => __('lang.comments'),
                'heading' => __('lang.comments'),
                'sidepanel_id' => 'sidepanel-filter-comments',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
        }

        //return
        return $page;
    }
}