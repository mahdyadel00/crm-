<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for time sheets
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Timesheets\CreateResponse;
use App\Http\Responses\Timesheets\DestroyResponse;
use App\Http\Responses\Timesheets\EditResponse;
use App\Http\Responses\Timesheets\IndexResponse;
use App\Http\Responses\Timesheets\StoreResponse;
use App\Http\Responses\Timesheets\UpdateResponse;
use App\Permissions\TaskPermissions;
use App\Repositories\TimerRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Timesheets extends Controller {

    /**
     * The timesheet repository instance.
     */
    protected $timerrepo;

    public function __construct(TimerRepository $timerrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('timesheetsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);
        $this->middleware('timesheetsMiddlewareEdit')->only([
            'update',
        ]);
        $this->middleware('timesheetsMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->timerrepo = $timerrepo;
    }

    /**
     * Display a listing of timesheets
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //only stopped timers
        request()->merge([
            'filter_timer_status' => 'stopped',
        ]);

        //get timesheets
        $timesheets = $this->timerrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('timesheets'),
            'timesheets' => $timesheets,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //response
        return new CreateResponse();
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function store(TaskPermissions $taskpermissions) {

        //validate - custom error messages
        $messages = [
            'timer_created.required' => __('lang.date') . '-' . __('lang.is_required'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'my_assigned_tasks' => [
                'required',
                Rule::exists('tasks', 'task_id'),
            ],
            'timer_created' => [
                'required',
                'date',
            ],
            'manual_time_hours' => [
                'required',
                'numeric',
            ],
            'manual_time_minutes' => [
                'required',
                'numeric',
            ],
        ], $messages);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //validate if user is assigned to this task
        if (!$taskpermissions->check('assigned', request('my_assigned_tasks'))) {
            abort(409, __('lang.you_are_now_not_assigned_to_this_task'));
        }

        //get task
        $task = \App\Models\Task::Where('task_id', request('my_assigned_tasks'))->first();

        //hours and minutes
        $hours = request('manual_time_hours') * 60 * 60;
        $minutes = request('manual_time_minutes') * 60;
        $total = $hours + $minutes;

        //validate
        if ($total <= 0) {
            abort(409, __('lang.total_time_must_be_greater_than_1_minute'));
        }

        $timer = new \App\Models\Timer();
        $timer->timer_creatorid = auth()->id();
        $timer->timer_created = request('timer_created');
        $timer->timer_time = $total;
        $timer->timer_taskid = $task->task_id;
        $timer->timer_projectid = $task->task_projectid;
        $timer->timer_clientid = $task->task_clientid;
        $timer->timer_status = 'stopped';
        $timer->save();

        //count sheets on this page
        if (request()->segment(2) == 'my') {
            request()->merge([
                'filter_timer_creatorid' => auth()->id(),
            ]);
        }
        $timesheets = $this->timerrepo->search();
        $count = $timesheets->total();

        //get refreshed timesheet
        $timesheets = $this->timerrepo->search($timer->timer_id);

        //reponse payload
        $payload = [
            'timesheets' => $timesheets,
            'count' => $count,
        ];

        //generate a response
        return new StoreResponse($payload);
    }

    /**
     * Show the form for editing the specified timesheet
     * @param object CategoryRepository instance of the repository
     * @param int $id timesheet id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //response
        return new EditResponse();
    }

    /**
     * Update the specified timesheetin storage.
     * @param object timesheetStoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @param int $id timesheet id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //hours and minutes
        $hours = request('time_hours') * 60 * 60;
        $minutes = request('time_minutes') * 60;
        $total = $hours + $minutes;

        //validate
        if ($total <= 0) {
            abort(409, __('lang.total_time_must_be_greater_than_1_minute'));
        }

        //update timer
        \App\Models\Timer::where('timer_id', $id)
            ->update(['timer_time' => $total]);

        //get updates
        $timesheets = $this->timerrepo->search($id);

        //reponse payload
        $payload = [
            'timesheets' => $timesheets,
            'time' => $total,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified resource from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //get the timer
                $timer = \App\Models\Timer::Where('timer_id', $id)->first();
                //delete client
                $timer->delete();
                //add to array
                $allrows[] = $id;
            }
        }
        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //generate a response
        return new DestroyResponse($payload);
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
                __('lang.time_sheets'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'timesheets',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_timesheets' => 'active',
            'mainmenu_sales' => 'active',
            'submenu_timesheets' => 'active',
            'sidepanel_id' => 'sidepanel-filter-timesheets',
            'dynamic_search_url' => url('timesheets/search?action=search&timesheetresource_id=' . request('timesheetresource_id') . '&timesheetresource_type=' . request('timesheetresource_type')),
            'add_button_classes' => '',
            'add_button_classes' => 'add-edit-item-button',
            'load_more_button_route' => 'timesheets',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.record_your_work_time'),
            'add_modal_create_url' => url('timesheets/create'),
            'add_modal_action_url' => url('timesheets'),
            'add_modal_action_ajax_class' => '',
            'add_modal_size' => 'modal-sm',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //projects list page
        if ($section == 'timesheets') {
            $page += [
                'meta_title' => __('lang.time_sheets'),
                'heading' => __('lang.time_sheets'),

            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //return
        return $page;
    }
}
