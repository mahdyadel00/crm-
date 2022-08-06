<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

// use App\Http\Responses\Foos\CreateResponse;
// use App\Repositories\FooRepository;
// use Illuminate\Validation\Rule;
// use Validator;

class Template extends Controller {

    /**
     * The foo repository instance.
     */
    protected $foorepo;

    public function __construct(FooRepository $foorepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('foosMiddlewareIndex')->only([
            'inded',
            'store',
        ]);

        $this->foorepo = $foorepo;
    }
    /**
     * Display a listing of foos
     * @return blade view | ajax view
     */
    public function index() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('index'),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * show form to edit a resource
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Update a resource
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('update'),
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //common settings
        $page = [

        ];

        //return
        return $page;
    }
}