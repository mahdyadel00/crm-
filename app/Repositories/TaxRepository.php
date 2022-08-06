<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for taxes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Tax;
use Illuminate\Http\Request;
use Log;

class TaxRepository {

    /**
     * The tax repository instance.
     */
    protected $tax;

    /**
     * Inject dependecies
     */
    public function __construct(Tax $tax) {
        $this->tax = $tax;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object tax collection
     */
    public function search() {

        $tax = $this->tax->newQuery();

        //joins
        $tax->leftJoin('taxrates', 'taxrates.taxrate_id', '=', 'tax.tax_taxrateid');

        // all client fields
        $tax->selectRaw('*');

        //filter resources
        if (request()->filled('taxresource_type')) {
            $tax->where('taxresource_type', request('taxresource_type'));
        }
        if (request()->filled('taxresource_id')) {
            $tax->where('taxresource_id', request('taxresource_id'));
        }

        //default sorting
        $tax->orderBy('tax_name', 'asc');

        // Get the results and return them.
        return $tax->get();
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return mixed int|bool
     */
    public function create($data = []) {

        //save new user
        $tax = new $this->tax;

        //data
        $tax->tax_taxrateid = $data['tax_taxrateid'];
        $tax->tax_name = $data['tax_name'];
        $tax->tax_rate = $data['tax_rate'];
        $tax->taxresource_type = $data['taxresource_type'];
        $tax->taxresource_id = $data['taxresource_id'];

        //save and return id
        if ($tax->save()) {
            return $tax->tax_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[TaxRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}