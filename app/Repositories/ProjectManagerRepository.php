<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for project managers
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\ProjectManager;
use Illuminate\Http\Request;
use Log;

class ProjectManagerRepository {

    /**
     * The manager repository instance.
     */
    protected $manager;

    /**
     * Inject dependecies
     */
    public function __construct(ProjectManager $manager) {
        $this->manager = $manager;
    }

    /**
     * Bulk delete manager users for a particular project
     * @param int $project_id the id of the project
     * @return null
     */
    public function delete($project_id = '') {

        //validations
        if (!is_numeric($project_id)) {
            Log::error("validation error - invalid params", ['process' => '[ProjectManagerRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $query = $this->manager->newQuery();
        $query->where('projectsmanager_projectid', '=', $project_id);
        $query->delete();
    }

    /**
     * manager new users to a project
     * @param int $project_id the id of the project
     * @return bool
     */
    public function add($project_id = '') {

        //add only to the specified user
        if (is_numeric(request('manager')) && is_numeric($project_id)) {
            $manager = new $this->manager;
            $manager->projectsmanager_projectid = $project_id;
            $manager->projectsmanager_userid = request('manager');
            $manager->save();
            return;
        }
    }

}