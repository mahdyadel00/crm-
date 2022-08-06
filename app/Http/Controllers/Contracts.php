<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for contract contracts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contracts\StoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Contracts\ChangeCategoryUpdateResponse;
use App\Http\Responses\Contracts\CreateResponse;
use App\Http\Responses\Contracts\DestroyResponse;
use App\Http\Responses\Proposals\EditCoverResponse;
use App\Http\Responses\Proposals\EditContentResponse;
use App\Http\Responses\Proposals\EditDetailsResponse;
use App\Http\Responses\Proposals\EditFinancialResponse;
use App\Http\Responses\Proposals\ShowPreviewResponse;
use App\Http\Responses\Contracts\EditResponse;
use App\Http\Responses\Contracts\IndexResponse;
use App\Http\Responses\Contracts\StoreResponse;
use App\Http\Responses\Contracts\UpdateResponse;
use App\Http\Responses\Proposals\ShowResponse;
use App\Models\Category;
use App\Models\Contract;
use App\Repositories\CategoryRepository;
use App\Repositories\ContractRepository;
use App\Repositories\UserRepository;
use App\Repositories\EstimateRepository;
use Illuminate\Http\Request;

class Contracts extends Controller {

    /**
     * The repository instances.
     */
    protected $contractrepo;
    protected $userrepo;
    protected $estimaterepo;

    public function __construct(ContractRepository $contractrepo, UserRepository $userrepo, EstimateRepository $estimaterepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('contractsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
        ]);

        $this->middleware('contractsMiddlewareEdit')->only([
            'edit',
            'update',
        ]);

        $this->middleware('contractsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('contractsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('contractsMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);

        //repos
        $this->contractrepo = $contractrepo;
        $this->userrepo = $userrepo;
        $this->estimaterepo = $estimaterepo;
    }

    /**
     * Display a listing of contracts
     * @param object CategoryRepository instance of the repository
     * @param object Category instance of the repository
     * @return blade view | ajax view
     */
    public function index(CategoryRepository $categoryrepo, Category $categorymodel) {

        //get contracts
        $contracts = $this->contractrepo->search();

        //get all categories (type: contract) - for filter panel
        $categories = $categoryrepo->get('contract');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('contracts'),
            'contracts' => $contracts,
            'count' => $contracts->count(),
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }


        /**
     * Display the specified contract
     * @param int $id contract id
     * @return \Illuminate\Http\Response
     */
    public function showDynamic($id) {

        //get the contract
        $contracts = $this->contractrepo->search($id);

        //contract
        $contract = $contracts->first();

        $page = $this->pageSettings('contract', $contract);

        //set dynamic url for use in template
        switch (request()->segment(3)) {
        case 'details':
            $page['dynamic_url'] = url('contracts/' . $contract->doc_id . '/edit/detials');
            $page['crumbs'] = [
                __('lang.contract'),
                __('lang.details'),
                '#' . $contract->formatted_id,
            ];
            break;
        case 'financial':
            $page['dynamic_url'] = url('contracts/' . $contract->doc_id . '/edit/financial');
            $page['crumbs'] = [
                __('lang.contract'),
                __('lang.financial'),
                '#' . $contract->formatted_id,
            ];
            break;
        case 'cover':
            $page['dynamic_url'] = url('contracts/' . $contract->doc_id . '/edit/cover');
            $page['crumbs'] = [
                __('lang.contract'),
                __('lang.cover'),
                '#' . $contract->formatted_id,
            ];
            break;
        default:
            $page['dynamic_url'] = url('contracts/' . $contract->doc_id . '/edit/content');
            $page['crumbs'] = [
                __('lang.contract'),
                __('lang.content'),
                '#' . $contract->formatted_id,
            ];
            break;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'contract' => $contract,
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Show the form for creating a new contract
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //client categories
        $categories = $categoryrepo->get('contract');

        //modal options
        config(['modal.action' => 'create']);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created contractin storage.
     * @param object StoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdate $request) {

        //create the contract
        $contract = new \App\Models\Contract();
        $contract->doc_creatorid = auth()->id();
        $contract->doc_categoryid = request('doc_categoryid');
        $contract->doc_client_id = request('doc_client_id');
        $contract->doc_project_id = request('doc_project_id');
        $contract->docresource_type = 'client';
        $contract->docresource_id = request('doc_client_id');
        $contract->doc_title = request('doc_title');
        $contract->doc_date_start = request('doc_date_start');
        $contract->doc_date_end = request('doc_date_end');
        $contract->save();

        //get the contract object (friendly for rendering in blade template)
        $contracts = $this->contractrepo->search($contract->doc_id);
    
        //create an estimate record
        $this->estimaterepo->createContractEstimate($contract->doc_id);

        //counting rows
        $rows = $this->contractrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'contracts' => $contracts,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the preview of the invvoice
     * @param int $id contract id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //set dynamic page
        $page['dynamic_url'] = url("contracts/$id/peview");

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new ShowResponse($payload);

    }

        /**
     * Show the resource
     * @return blade view | ajax view
     */
    public function showPreview($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new ShowPreviewResponse($payload);
    }

    /**
     * edit the cover
     * @return blade view | ajax view
     */
    public function editCover($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new EditCoverResponse($payload);
    }

    /**
     * edit the cover
     * @return blade view | ajax view
     */
    public function editDetails($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new EditDetailsResponse($payload);
    }

    /**
     * edit the cover
     * @return blade view | ajax view
     */
    public function editFinancial($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new EditFinancialResponse($payload);
    }

    /**
     * edit the cover
     * @return blade view | ajax view
     */
    public function editContent($id) {

        //get the project
        $contracts = $this->contractrepo->search($id);
        $contract = $contracts->first();

        //set page
        $page = $this->pageSettings('contract', $contract);

        //payload
        $payload = [
            'contract' => $contract,
            'page' => $page,
        ];

        //show the view
        return new EditContentResponse($payload);
    }

    /**
     * Show the form for editing the specified contract
     * @param object CategoryRepository instance of the repository
     * @param int $id contract id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the contract
        $contract = $this->contractrepo->search($id);

        //client categories
        $categories = $categoryrepo->get('contract');

        //not found
        if (!$contract = $contract->first()) {
            abort(409, __('lang.contract_not_found'));
        }

        //modal options
        config(['modal.action' => 'edit']);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'contract' => $contract,
            'categories' => $categories,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified contractin storage.
     * @param object StoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @param int $id contract id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdate $request, $id) {

        //get the contract
        $contract = \App\Models\Contract::Where('doc_id', $id)->first();
        $contract->doc_categoryid = request('doc_categoryid');
        $contract->doc_title = request('doc_title');
        $contract->doc_date_start = request('doc_date_start');
        $contract->doc_date_end = request('doc_date_end');
        $contract->save();

        //get the contract
        $contracts = $this->contractrepo->search($id);

        //reponse payload
        $payload = [
            'contracts' => $contracts,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified contract from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked contracts
            if ($value == 'on') {
                //get the contract
                $contract = \App\Models\Contract::Where('doc_id', $id)->first();
                //delete client
                $contract->delete();
                //add to array
                $allrows[] = $id;
            }
        }
        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //generate a response
        return new DestroyResponse($payload);
    }

    /**
     * Bulk change category for contracts
     * @url baseusr/contracts/bulkdelete
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete() {

        //validation - post
        if (!is_array(request('contract'))) {
            abort(409);
        }

        //loop through and delete each one
        $deleted = 0;
        foreach (request('contract') as $contract_id => $value) {
            if ($value == 'on') {
                //get the contract
                if ($contracts = $this->contractrepo->search($contract_id)) {
                    //remove the contract
                    $contracts->first()->delete();
                    //hide and remove row
                    $jsondata['dom_visibility'][] = array(
                        'selector' => '#contract_' . $contract_id,
                        'action' => 'slideup-remove',
                    );
                }
                $deleted++;
            }
        }

        //something went wrong
        if ($deleted == 0) {
            abort(409);
        }

        //success
        $jsondata['notification'] = array('type' => 'success', 'value' => 'Request has been completed');

        //ajax response
        return response()->json($jsondata);
    }

    /**
     * Show the form for updating the contract
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all contract categories
        $categories = $categoryrepo->get('contract');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the contract
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'contract')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each contract
        $allrows = array();
        foreach (request('ids') as $contract_id => $value) {
            if ($value == 'on') {
                $contract = \App\Models\Contract::Where('doc_id', $contract_id)->first();
                //update the category
                $contract->doc_categoryid = request('category');
                $contract->save();
                //get the contract in rendering friendly format
                $contracts = $this->contractrepo->search($contract_id);
                //add to array
                $allrows[] = $contracts;
            }
        }

        //reponse payload
        $payload = [
            'allrows' => $allrows,
        ];

        //show the form
        return new ChangeCategoryUpdateResponse($payload);
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
                __('lang.sales'),
                __('lang.contracts'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'contracts',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_sales' => 'active',
            'mainmenu_contracts' => 'active',
            'submenu_contracts' => 'active',
            'sidepanel_id' => 'sidepanel-filter-contracts',
            'dynamic_search_url' => url('contracts/search?action=search&contractresource_id=' . request('contractresource_id') . '&contractresource_type=' . request('contractresource_type')),
            'load_more_button_route' => 'contracts',
            'source' => 'list',
        ];

        //contracts list page
        if ($section == 'contracts') {
            $page += [
                'meta_title' => __('lang.contracts'),
                'heading' => __('lang.contracts'),
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //contracts list page
        if ($section == 'contract') {

            //adjust
            $page['page'] = 'contract';

            //crumbs
            $page['crumbs'] = [
                __('lang.contract'),
                '#' . $data->formmatted_id,
            ];

            $page += [
                'meta_title' => __('lang.contract'),
                'heading' => __('lang.contract'),
            ];

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