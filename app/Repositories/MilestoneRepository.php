<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for milestones
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Milestone;
use Illuminate\Support\Facades\Schema;
use Log;

class MilestoneRepository {

    /**
     * The leads repository instance.
     */
    protected $milestone;

    /**
     * Inject dependecies
     */
    public function __construct(Milestone $milestone) {
        $this->milestone = $milestone;
    }

    /**
     * Bulk add tags
     * @param string $milestone_title milestone title
     * @param int $project_id the id of the project
     * @return null
     */
    public function add($project_id = '', $milestone_title = '') {

        //validations
        if ($milestone_title == '' || !is_numeric($project_id)) {
            return false;
        }

        //add milestone
        $milestone = new $this->milestone;
        $milestone->milestone_projectid = $project_id;
        $milestone->milestone_title = $milestone_title;
        $milestone->milestone_creatorid = Auth()->user()->id;
        $milestone->save();
    }

    /**
     * Bulk add tags
     * @param string $milestone_title milestone title
     * @param int $project_id the id of the project
     * @return null
     */
    public function addUncategorised($project_id = '', $position = 1) {

        //validations
        if (!is_numeric($project_id)) {
            return false;
        }

        //add milestone
        $milestone = new $this->milestone;
        $milestone->milestone_projectid = $project_id;
        $milestone->milestone_title = 'uncategorised';
        $milestone->milestone_creatorid = Auth()->user()->id;
        $milestone->milestone_type = 'uncategorised';
        $milestone->milestone_position = $position;
        $milestone->save();
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object milestone collection
     */
    public function search($id = '') {

        //new query
        $milestones = $this->milestone->newQuery();

        // all client fields
        $milestones->selectRaw('*');

        //count tasks - all
        $milestones->selectRaw('(SELECT COUNT(*)
                                             FROM tasks
                                             WHERE task_milestoneid = milestones.milestone_id)
                                             AS milestone_count_tasks_all');
        //count tasks - pending
        $milestones->selectRaw("(SELECT COUNT(*)
                                             FROM tasks
                                             WHERE task_milestoneid = milestones.milestone_id
                                             AND task_status NOT IN('completed'))
                                             AS milestone_count_tasks_pending");

        //count tasks - all
        $milestones->selectRaw("(SELECT COUNT(*)
                                             FROM tasks
                                             WHERE task_milestoneid = milestones.milestone_id
                                             AND task_status = 'completed')
                                             AS milestone_count_tasks_completed");

        //default where
        $milestones->whereRaw("1 = 1");

        //filters: project id
        if (request()->filled('filter_milestone_projectid')) {
            $milestones->where('milestone_projectid', request('filter_milestone_projectid'));
        }

        //filters: id
        if (request()->filled('filter_milestone_id')) {
            $milestones->where('milestone_id', request('filter_milestone_id'));
        }
        if (is_numeric($id)) {
            $milestones->where('milestone_id', $id);
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('milestones', request('orderby'))) {
                $milestones->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'total_tasks':
                $milestones->orderBy('milestone_count_tasks_all', request('sortorder'));
                break;
            case 'pending_tasks':
                $milestones->orderBy('milestone_count_tasks_pending', request('sortorder'));
                break;
            case 'completed_tasks':
                $milestones->orderBy('milestone_count_tasks_completed', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $milestones->orderBy('milestone_position', 'asc');
        }

        return $milestones->paginate();
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $milestone = new $this->milestone;

        //data
        $milestone->milestone_title = request('milestone_title');
        $milestone->milestone_creatorid = auth()->id();
        $milestone->milestone_projectid = request('milestone_projectid');

        //save and return id
        if ($milestone->save()) {
            return $milestone->milestone_id;
        } else {
            Log::error("validation error - invalid params", ['process' => '[MilestoneRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$milestone = $this->milestone->find($id)) {
            Log::error("milestone record could not be found", ['process' => '[MilestoneRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'milestone_id' => $id ?? '']);
            return false;
        }

        //general
        $milestone->milestone_title = request('milestone_title');

        //save
        if ($milestone->save()) {
            return $milestone->milestone_id;
        } else {
            Log::error("record could not be save - database error", ['process' => '[MilestoneRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * return all milestones for a project
     *
     * @param int $id project id
     * @return object project model object
     */
    public function autocompleteProjectsMilestones($id = '') {

        //validate
        //sanity
        if (!is_numeric($id)) {
            return [];
        }

        //start
        $query = $this->milestone->newQuery();
        $query->selectRaw("milestone_title AS value, milestone_id AS id");

        //ignore project templates
        $query->where('milestone_projectid', $id);

        //return
        return $query->get();
    }
}