<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @foo    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Block;
use Illuminate\Http\Request;

class DocumentsRepository {

    /**
     * The block repository instance.
     */
    protected $block;

    /**
     * Inject dependecies
     */
    public function __construct(Block $block) {
        $this->block = $block;
    }

}