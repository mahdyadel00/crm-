<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for milestone categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\MilestoneCategory;
use Log;

class MilestoneCategoryRepository {

    /**
     * The category repository instance.
     */
    protected $category;

    /**
     * Inject dependecies
     */
    public function __construct(MilestoneCategory $category) {
        $this->category = $category;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object milestone collection
     */
    public function search($id = '') {

        $categories = $this->category->newQuery();

        //joins
        $categories->leftJoin('users', 'users.id', '=', 'milestone_categories.milestonecategory_creatorid');

        // all client fields
        $categories->selectRaw('*');

        if (is_numeric($id)) {
            $categories->where('milestonecategory_id', $id);
        }

        //default sorting
        $categories->orderBy('milestonecategory_position', 'asc');

        // Get the results and return them.
        return $categories->paginate(10000);
    }

    /**
     * Create a new record
     * @param int $position new record position
     * @return mixed object|bool
     */
    public function create($position = '') {

        //validate
        if (!is_numeric($position)) {
            Log::error("validation error - invalid params", ['process' => '[MilestoneCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save
        $category = new $this->category;

        //data
        $category->milestonecategory_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('milestonecategory_title'));
        $category->milestonecategory_creatorid = auth()->id();
        $category->milestonecategory_position = $position;

        //save and return id
        if ($category->save()) {
            return $category->milestonecategory_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[MilestoneCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function update($id) {

        //get the record
        if (!$category = $this->category->find($id)) {
            return false;
        }

        //general
        $category->milestonecategory_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('milestonecategory_title'));

        //save
        if ($category->save()) {
            return $category->milestonecategory_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[MilestoneCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * add all the default milestone categories for a new project
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function addProjectMilestones($project) {

        //position to be used for saving uncategories milestone category
        $position = 1;

        if (!$project instanceof \App\Models\Project) {
            return $position;
        }

        //get all default project categories
        $milestone_categories = $this->category->newQuery();
        $milestone_categories->orderBy('milestonecategory_position', 'asc');
        $categories = $milestone_categories->get();

        //save each default milestone, as a milestone for this project
        foreach ($categories as $category) {
            $milestone = new \App\Models\Milestone();
            $milestone->milestone_creatorid = 0;
            $milestone->milestone_title = $category->milestonecategory_title;
            $milestone->milestone_projectid = $project->project_id;
            $milestone->milestone_type = 'categorised';
            $milestone->save();

            //increment position
            $position++;
        }

        //return position
        return $position;
    }
}