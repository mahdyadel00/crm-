<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for contracts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\ContractTemaple;
use Illuminate\Http\Request;
//use DB;
//use Illuminate\Support\Facades\Schema;

class ContractTemplateRepository{



    /**
     * The contract templates repository instance.
     */
    protected $templates;

    /**
     * Inject dependecies
     */
    public function __construct(ContractTemplate $templates) {
        $this->templates = $templates;
    }

}