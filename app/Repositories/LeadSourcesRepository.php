<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for leads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\LeadSources;
use Log;

class LeadSourcesRepository {

    /**
     * The lead sources repository instance.
     */
    protected $sources;

    /**
     * Inject dependecies
     */
    public function __construct(LeadSources $sources) {
        $this->sources = $sources;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object lead source collection
     */
    public function search($id = '') {

        $sources = $this->sources->newQuery();

        //joins
        $sources->leftJoin('users', 'users.id', '=', 'leads_sources.leadsources_creatorid');

        // all client fields
        $sources->selectRaw('*');

        if (is_numeric($id)) {
            $sources->where('leadsources_id', $id);
        }

        //default sorting
        $sources->orderBy('leadsources_title', 'desc');

        // Get the results and return them.
        return $sources->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $leadsource = new $this->sources;

        //data
        $leadsource->leadsources_title = request('leadsources_title');
        $leadsource->leadsources_creatorid = auth()->id();

        //save and return id
        if ($leadsource->save()) {
            return $leadsource->leadsources_id;
        } else {
            Log::error("validation error - invalid params", ['process' => '[LeadSourceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if (!$leadsource = $this->sources->find($id)) {
            return false;
        }

        //general
        $leadsource->leadsources_title = request('leadsources_title');

        //save
        if ($leadsource->save()) {
            return $leadsource->leadsources_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[LeadSourceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}