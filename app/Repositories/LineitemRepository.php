<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for line items
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Lineitem;
use Illuminate\Http\Request;
use Log;

class LineitemRepository {

    /**
     * The items repository instance.
     */
    protected $lineitems;

    /**
     * Inject dependecies
     */
    public function __construct(Lineitem $lineitems) {
        $this->lineitems = $lineitems;
    }

    /**
     * Search model
     * @return object lineitem collection
     */
    public function search() {

        $lines = $this->lineitems->newQuery();

        // all client fields
        $lines->selectRaw('*');

        //filter resources
        if (request()->filled('lineitemresource_type')) {
            $lines->where('lineitemresource_type', request('lineitemresource_type'));
        }
        if (request()->filled('lineitemresource_id')) {
            $lines->where('lineitemresource_id', request('lineitemresource_id'));
        }

        //default sorting
        $lines->orderBy('lineitem_position', 'asc');

        // Get the results and return them.
        return $lines->get();
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return mixed int|bool
     */
    public function create($data = []) {

        //save new user
        $lineitem = new $this->lineitems;

        //data
        $lineitem->lineitem_position = $data['lineitem_position'];
        $lineitem->lineitem_description = $data['lineitem_description'];
        $lineitem->lineitem_rate = $data['lineitem_rate'];
        $lineitem->lineitem_unit = $data['lineitem_unit'];
        $lineitem->lineitem_quantity = $data['lineitem_quantity'];
        $lineitem->lineitem_time_hours = $data['lineitem_time_hours'];
        $lineitem->lineitem_time_minutes = $data['lineitem_time_minutes'];
        $lineitem->lineitem_total = $data['lineitem_total'];
        $lineitem->lineitemresource_linked_type = $data['lineitemresource_linked_type'];
        $lineitem->lineitemresource_linked_id = $data['lineitemresource_linked_id'];
        $lineitem->lineitemresource_type = $data['lineitemresource_type'];
        $lineitem->lineitemresource_id = $data['lineitemresource_id'];
        $lineitem->lineitem_type = $data['lineitem_type'];
        $lineitem->lineitem_time_timers_list = $data['lineitem_time_timers_list'];

        //save and return the new id
        if ($lineitem->save()) {
            return $lineitem->lineitem_id;
        } else {
            Log::error("record could not be created - database error", ['process' => '[LineItemRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * get the next line items position number
     * @return int next line item position
     */
    public function nextLinePosition() {

        //get last record items position
        if ($last = \App\Models\Lineitem::orderBy('lineitem_position', 'desc')->first()) {
            $position = $last->lineitem_position + config('settings.db_position_increment');
        } else {
            $position = config('settings.db_position_increment');
        }

        return $position;
    }

}