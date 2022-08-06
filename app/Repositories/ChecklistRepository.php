<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for checklists
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Checklist;
use Illuminate\Http\Request;
use Log;

//use DB;
//use Illuminate\Support\Facades\Schema;

class ChecklistRepository {

    /**
     * The checklists repository instance.
     */
    protected $checklists;

    /**
     * Inject dependecies
     */
    public function __construct(Checklist $checklists) {
        $this->checklists = $checklists;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object checklists collection
     */
    public function search($id = '') {

        //new query
        $checklists = $this->checklists->newQuery();

        // all client fields
        $checklists->selectRaw('*');

        //default where
        $checklists->whereRaw("1 = 1");

        //limit by id
        if (is_numeric($id)) {
            $checklists->where('checklist_id', $id);
        }

        //filters: resource type
        if (request()->filled('checklistresource_type')) {
            $checklists->where('checklistresource_type', request('checklistresource_type'));
        }

        //filters: resource type
        if (request()->filled('checklistresource_id')) {
            $checklists->where('checklistresource_id', request('checklistresource_id'));
        }

        //filter clients
        if (request()->filled('filter_checklist_clientid')) {
            $invoices->where('checklist_clientid', request('filter_checklist_clientid'));
        }

        //default sorting
        $checklists->orderBy('checklist_id', 'desc');

        return $checklists->paginate(1000);
    }

    /**
     * Create a new record
     * @return mixed object|bool object or process outcome
     */
    public function create($position = 0) {

        //save new user
        $checklist = new $this->checklists;

        //data
        $checklist->checklist_creatorid = auth()->id();
        $checklist->checklist_text = request('checklist_text');
        $checklist->checklistresource_type = request('checklistresource_type');
        $checklist->checklistresource_id = request('checklistresource_id');
        $checklist->checklist_position = $position;


        //save and return id
        if ($checklist->save()) {
            return $checklist->checklist_id;
        } else {
            Log::error("creating record failed - database error", ['process' => '[ChecklistRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}