<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\updating;

use App\Http\Controllers\Controller;
use App\Http\Responses\Foos\IndexResponse;
use App\Repositories\FooRepository;
use Illuminate\Http\Request;

// use Illuminate\Validation\Rule;
// use Validator;

class Runonce extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

    public function __construct(FooRepository $foorepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->foorepo = $foorepo;
    }

    /**
     * Display a listing of foos
     * @url baseusr/foos?page=1&source=ext&action=load
     * @urlquery
     *    - [page] numeric|null (pagination page number)
     *    - [source] ext|null  (ext: when called from embedded pages)
     *    - [action] load | null (load: when making additional ajax calls)
     * @return blade view | ajax view
     */
    public function index() {

        //get team members
        $foos = $this->foorepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('foos'),
            'foos' => $foos,
        ];

        //show the view
        return new IndexResponse($payload);
    }
}