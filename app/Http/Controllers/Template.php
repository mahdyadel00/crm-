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
// use App\Http\Responses\Foos\DestroyResponse;
// use App\Http\Responses\Foos\EditResponse;
use App\Http\Responses\Foos\IndexResponse;
// use App\Http\Responses\Foos\StoreResponse;
// use App\Http\Responses\Foos\UpdateResponse;
use App\Repositories\FooRepository;
use Illuminate\Http\Request;

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

        //custom error messages
        $messages = [
            'foo_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'foo_title' => 'required',
            'foo_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //create the foo
        if (!$foo_id = $this->foorepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the foo object (friendly for rendering in blade template)
        $foos = $this->foorepo->search($foo_id);

        //reponse payload
        $payload = [
            'foos' => $foos,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the foo
        $foo = $this->foorepo->search($id);

        //not found
        if (!$foo = $foo->first()) {
            abort(409, __('lang.hello'));
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'foo' => $foo,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the foo
        $foo = $this->foorepo->search($id);

        //not found
        if (!$foo = $foo->first()) {
            abort(409, 'The requested foo could not be loaded');
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'foo' => $foo,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [
            'foo_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'foo_title' => 'required',
            'foo_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->foorepo->update($id)) {
            abort(409);
        }

        //get foo
        $foos = $this->foorepo->search($id);

        //reponse payload
        $payload = [
            'foos' => $foos,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the foo
        if (!$foos = $this->foorepo->search($id)) {
            abort(409);
        }

        //remove the foo
        $foos->first()->delete();

        //reponse payload
        $payload = [
            'foo_id' => $id,
        ];

        //process reponse
        return new DestroyResponse($payload);
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
            'crumbs' => [
                'foos',
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'foos',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_foos' => 'active',
            'sidepanel_id' => 'sidepanel-filter-foos',
            'dynamic_search_url' => url('foos/search?action=search&fooresource_id=' . request('fooresource_id') . '&fooresource_type=' . request('fooresource_type')),
            'load_more_button_route' => 'foos',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_create_url' => url('foos/create?fooresource_id=' . request('fooresource_id') . '&fooresource_type=' . request('fooresource_type')),
            'add_modal_action_url' => url('foos?fooresource_id=' . request('fooresource_id') . '&fooresource_type=' . request('fooresource_type')),
        ];

        //foos list page
        if ($section == 'foos') {
            $page += [
                'meta_title' => 'Foos',
                'heading' => 'Foos',
                'sidepanel_id' => 'sidepanel-filter-foos',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //create new resource
        if ($section == 'create') {
            $page += [
                'section' => 'create',
            ];
            return $page;
        }

        //edit new resource
        if ($section == 'edit') {
            $page += [
                'section' => 'edit',
            ];
            return $page;
        }

        //return
        return $page;
    }
}