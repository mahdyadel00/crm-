<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\WebForm;
use Illuminate\Http\Request;
use Log;

class WebformRepository {

    /**
     * The leads repository instance.
     */
    protected $webform;

    /**
     * Inject dependecies
     */
    public function __construct(Webform $webform) {
        $this->webform = $webform;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object webforms collection
     */
    public function search($id = '') {

        $webforms = $this->webform->newQuery();

        // all client fields
        $webforms->selectRaw('*');

        //joins
        $webforms->leftJoin('users', 'users.id', '=', 'webforms.webform_creatorid');

        //default where
        $webforms->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_webform_id')) {
            $webforms->where('webform_id', request('filter_webform_id'));
        }
        if (is_numeric($id)) {
            $webforms->where('webform_id', $id);
        }

        //default sorting
        $webforms->orderBy('webform_title', 'asc');

        // Get the results and return them.
        return $webforms->paginate(1000);
    }

    /**
     * Create a new record
     * @return mixed int|bool webform_uniqueid
     */
    public function create() {

        //save new user
        $webform = new $this->webform;

        //data
        $webform->webform_title = request('webform_title');
        $webform->webform_creatorid = auth()->id();
        $webform->webform_uniqueid = str_unique();
        $webform->webform_submit_button_text = __('lang.submit');

        //save and return id
        if ($webform->save()) {
            return $webform->webform_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[WebformRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if (!$webform = $this->webform->find($id)) {
            return false;
        }

        //general
        $webform->webform_title = request('webform_title');

        //save
        if ($webform->save()) {
            return $webform->webform_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[WebformRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}