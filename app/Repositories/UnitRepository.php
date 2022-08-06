<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for units
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitRepository {

    /**
     * The units repository instance.
     */
    protected $units;

    /**
     * Inject dependecies
     */
    public function __construct(Unit $units) {
        $this->units = $units;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object unit collection
     */
    public function search($id = '') {

        $units = $this->units->newQuery();

        //joins
        $units->leftJoin('users', 'users.id', '=', 'units.unit_creatorid');

        // all client fields
        $units->selectRaw('*');

        if (is_numeric($id)) {
            $units->where('unit_id', $id);
        }

        //default sorting
        $units->orderBy('unit_name', 'asc');

        // Get the results and return them.
        return $units->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $unit = new $this->units;

        //data
        $unit->unit_name = request('unit_name');
        $unit->unit_creatorid = auth()->id();

        //save and return id
        if ($unit->save()) {
            return $unit->unit_id;
        } else {
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
        if (!$unit = $this->units->find($id)) {
            return false;
        }

        //general
        $unit->unit_name = request('unit_name');

        //save
        if ($unit->save()) {
            return $unit->unit_id;
        } else {
            return false;
        }
    }

    /**
     * update based on free text entered by user
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function updateList($unit_name = '') {

        //validate
        if ($unit_name == '') {
            return;
        }

        if (\App\Models\Unit::Where('unit_name', $unit_name)->exists()) {
            return;
        }

        //save new user
        $unit = new $this->units;
        $unit->unit_name = $unit_name;
        $unit->unit_creatorid = auth()->id();
    }

}