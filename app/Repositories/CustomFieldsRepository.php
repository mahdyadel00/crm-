<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\CustomField;
use Illuminate\Http\Request;

class CustomFieldsRepository {

    /**
     * The leads repository instance.
     */
    protected $customfields;

    /**
     * Inject dependecies
     */
    public function __construct(CustomField $customfield) {
        $this->customfields = $customfield;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object customfields collection
     */
    public function search($id = '') {

        $customfields = $this->customfields->newQuery();

        // all client fields
        $customfields->selectRaw('*');

        //default where
        $customfields->whereRaw("1 = 1");

        //type
        if (request()->filled('customfields_type')) {
            $customfields->where('customfields_type', request('customfields_type'));
        }

        //field
        if (request()->filled('filter_field_type')) {
            $customfields->where('customfields_datatype', request('filter_field_type'));
        }

        //standard form
        if (request()->filled('filter_show_standard_form_status')) {
            $customfields->where('customfields_standard_form_status', request('filter_show_standard_form_status'));
        }

        //field status
        if (request()->filled('filter_field_status')) {
            $customfields->where('customfields_status', request('filter_field_status'));
        }

        //sorting
        if (request('sort_by') == 'customfields_position') {
            $customfields->orderBy('customfields_position', 'asc');
        } else {
            $customfields->orderBy('customfields_sorting_a_z', 'ASC'); //put NULL title items last
            $customfields->orderBy('customfields_title', 'ASC'); //put NULL title items last
        }

        // Get the results and return them.
        return $customfields->paginate(config('settings.custom_fields_display_limit'));
    }

}