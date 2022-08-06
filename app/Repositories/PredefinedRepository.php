<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for predefined responses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Predefined;
use Illuminate\Http\Request;
//use DB;
//use Illuminate\Support\Facades\Schema;

class PredefinedRepository{



    /**
     * The predefined repository instance.
     */
    protected $predefined;

    /**
     * Inject dependecies
     */
    public function __construct(Predefined $predefined) {
        $this->predefined = $predefined;
    }

}