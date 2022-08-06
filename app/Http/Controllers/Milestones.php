<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for milestones
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Milestones\CreateResponse;
use App\Http\Responses\Milestones\DestroyResponse;
use App\Http\Responses\Milestones\EditResponse;
use App\Http\Responses\Milestones\IndexResponse;
use App\Http\Responses\Milestones\StoreResponse;
use App\Http\Responses\Milestones\UpdateResponse;
use App\Repositories\MilestoneRepository;
use Illuminate\Http\Request;
use Validator;

class Milestones extends Controller {

    /**
     * The milestone repository instance.
     */
    protected $milestonerepo;

    public function __construct(MilestoneRepository $milestonerepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('milestonesMiddlewareIndex')->only([
            'index',
            'store',
            'update',
        ]);

        //route middleware
        $this->middleware('milestonesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        //route middleware
        $this->middleware('milestonesMiddlewareEdit')->only([
            'edit',
            'update',
        ]);

        //route middleware
        $this->middleware('milestonesMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->milestonerepo = $milestonerepo;
    }

    /**
     * Display a listing of milestones
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //basic page settings
        $page = $this->pageSettings('milestones');

        //get project milestones
        $milestones = $this->milestonerepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'milestones' => $milestones,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new milestone
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //page settings
        $page = $this->pageSettings('create');

        //reponse payload
        $payload = [
            'page' => $page,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created milestone in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'milestone_title' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            
            abort(409, $messages);
        }

        //avoid duplicate
        if (\App\Models\Milestone::where('milestone_title', request('milestone_title'))
            ->where('milestone_projectid', request('milestone_projectid'))
            ->exists()) {
            abort(409, __('lang.milestone_already_exists'));
        }

        //create the milestone
        if (!$milestone_id = $this->milestonerepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the milestone object (friendly for rendering in blade template)
        $milestones = $this->milestonerepo->search($milestone_id);

        //counting rows
        $rows = $this->milestonerepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'milestones' => $milestones,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified milestone.
     * @param int $id milestone id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the milestone
        $milestone = $this->milestonerepo->search($id);

        //not found
        if (!$milestone = $milestone->first()) {
            abort(409, __('lang.milestone_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'milestone' => $milestone,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified milestone in storage.
     * @param int $id milestone id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'milestone_title' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            
            abort(409, $messages);
        }

        //avoid duplicate
        if (\App\Models\Milestone::where('milestone_title', request('milestone_title'))
            ->where('milestone_projectid', request('milestone_projectid'))
            ->where('milestone_id', '!=', $id)
            ->exists()) {
            abort(409, __('lang.milestone_already_exists'));
        }

        //update
        if (!$this->milestonerepo->update($id)) {
            abort(409);
        }

        //get milestone
        $milestones = $this->milestonerepo->search($id);

        //reponse payload
        $payload = [
            'milestones' => $milestones,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified milestone from storage.
     * @param int $id milestone id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the milestone
        $milestones = $this->milestonerepo->search($id);
        $milestone = $milestones->first();

        //do not delete default milstone
        if ($milestone->milestone_type == 'uncategorised') {
            abort(409, __('lang.you_cannot_delete_system_default_item'));
        }

        //are we deleteing the tasks
        if (request('delete_milestone_tasks') == 'on') {

            //delete tasks
            foreach ($milestone->tasks as $task) {

                //delete attachements
                foreach ($task->attachments()->get() as $attachment) {
                    if ($attachment->attachment_directory != '') {
                        if (Storage::exists("files/$attachment->attachment_directory")) {
                            Storage::deleteDirectory("files/$attachment->attachment_directory");
                        }
                    }
                    $attachment->delete();
                }

                //delete comments
                $task->comments()->delete();

                //delete timers
                $task->timers()->delete();

                //delete comments
                $task->comments()->delete();

                //delete events
                $task->events()->delete();

                //delete task
                $task->delete();

            }

        } else {
            //move the tasks to default milestone
            if ($default = \App\Models\Milestone::Where('milestone_projectid', $milestone->milestone_projectid)->Where('milestone_type', 'uncategorised')->first()) {
                //update
                \App\Models\Task::Where('task_milestoneid', $milestone->milestone_id)->update(['task_milestoneid' => $default->milestone_id]);
            }
        }

        //remove the milestone
        $milestone->delete();

        //reponse payload
        $payload = [
            'milestone_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
    }

    /**
     * Update milestone positions
     * @param int $id milestone id
     * @return \Illuminate\Http\Response
     */
    public function updatePositions() {

        //reposition each lead status
        $i = 1;
        foreach (request('sort-milestones') as $key => $id) {
            if (is_numeric($id)) {
                \App\Models\Milestone::where('milestone_id', $id)->update(['milestone_position' => $i]);
            }
            $i++;
        }

        //retun simple success json
        return response()->json('success', 200);
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
                __('lang.milestone'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'milestones',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_milestones' => 'active',
            'sidepanel_id' => 'sidepanel-filter-milestones',
            'dynamic_search_url' => url('milestones/search?action=search&milestoneresource_id=' . request('milestoneresource_id') . '&milestoneresource_type=' . request('milestoneresource_type')),
            'add_button_classes' => 'add-edit-milestone-button',
            'load_more_button_route' => 'milestones',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_milestone'),
            'add_modal_create_url' => url('milestones/create?milestoneresource_id=' . request('milestoneresource_id') . '&milestoneresource_type=' . request('milestoneresource_type')),
            'add_modal_action_url' => url('milestones?milestoneresource_id=' . request('milestoneresource_id') . '&milestoneresource_type=' . request('milestoneresource_type') . '&count=' . ($data['count'] ?? '')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //milestones list page
        if ($section == 'milestones') {
            $page += [
                'meta_title' => __('lang.milestone'),
                'heading' => __('lang.milestone'),
                'sidepanel_id' => 'sidepanel-filter-milestones',
            ];
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {
            $page += [
                'list_page_actions_size' => 'col-lg-12',

            ];
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //return
        return $page;
    }
}