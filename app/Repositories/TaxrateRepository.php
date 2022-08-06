<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for tax rates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Taxrate;
use Illuminate\Http\Request;
use Log;

class TaxrateRepository {

    /**
     * The taxrates repository instance.
     */
    protected $taxrates;

    /**
     * Inject dependecies
     */
    public function __construct(Taxrate $taxrates) {
        $this->taxrates = $taxrates;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object taxrate collection
     */
    public function search($id = '') {

        $taxrates = $this->taxrates->newQuery();

        //joins
        $taxrates->leftJoin('users', 'users.id', '=', 'taxrates.taxrate_creatorid');

        // all client fields
        $taxrates->selectRaw('*');

        if (is_numeric($id)) {
            $taxrates->where('taxrate_id', $id);
        }

        //default sorting
        $taxrates->orderBy('taxrate_name', 'asc');

        // Get the results and return them.
        return $taxrates->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $taxrate = new $this->taxrates;

        //data
        $taxrate->taxrate_name = str_replace('|', '', request('taxrate_name')); //[sanity] remove a special character | that we need and are using in <js>
        $taxrate->taxrate_value = request('taxrate_value');
        $taxrate->taxrate_creatorid = auth()->id();
        $taxrate->taxrate_uniqueid = str_unique();

        //save and return id
        if ($taxrate->save()) {
            return $taxrate->taxrate_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[TaaxRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}