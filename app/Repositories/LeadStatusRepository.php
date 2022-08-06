<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for lead statuses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\LeadStatus;
use Log;

class LeadStatusRepository {

    /**
     * The leads repository instance.
     */
    protected $status;

    /**
     * Inject dependecies
     */
    public function __construct(LeadStatus $status) {
        $this->status = $status;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object lead status collection
     */
    public function search($id = '') {

        $status = $this->status->newQuery();

        //joins
        $status->leftJoin('users', 'users.id', '=', 'leads_status.leadstatus_creatorid');

        // all client fields
        $status->selectRaw('*');

        //count leads
        $status->selectRaw('(SELECT COUNT(*)
                                      FROM leads
                                      WHERE lead_status = leads_status.leadstatus_id)
                                      AS count_leads');
        if (is_numeric($id)) {
            $status->where('leadstatus_id', $id);
        }

        //default sorting
        $status->orderBy('leadstatus_position', 'asc');

        // Get the results and return them.
        return $status->paginate(10000);
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function update($id) {

        //get the record
        if (!$status = $this->status->find($id)) {
            return false;
        }

        //general
        $status->leadstatus_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('leadstatus_title'));
        $status->leadstatus_color = request('leadstatus_color');

        //save
        if ($status->save()) {
            return $status->leadstatus_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[LeadStatusRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @param int $position position of new record
     * @return mixed object|bool
     */
    public function create($position = '') {

        //validate
        if (!is_numeric($position)) {
            Log::error("error creating a new lead status record in DB - (position) value is invalid", ['process' => '[create()]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save
        $status = new $this->status;

        //data
        $status->leadstatus_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('leadstatus_title'));
        $status->leadstatus_color = request('leadstatus_color');
        $status->leadstatus_creatorid = auth()->id();
        $status->leadstatus_position = $position;

        //save and return id
        if ($status->save()) {
            return $status->leadstatus_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[LeadStatusRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}