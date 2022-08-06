<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for product items
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Items\ItemStoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Items\ChangeCategoryUpdateResponse;
use App\Http\Responses\Items\CreateResponse;
use App\Http\Responses\Items\DestroyResponse;
use App\Http\Responses\Items\EditResponse;
use App\Http\Responses\Items\IndexResponse;
use App\Http\Responses\Items\StoreResponse;
use App\Http\Responses\Items\UpdateResponse;
use App\Models\Category;
use App\Models\Item;
use App\Repositories\CategoryRepository;
use App\Repositories\ItemRepository;
use App\Repositories\UnitRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class Items extends Controller {

    /**
     * The item repository instance.
     */
    protected $itemrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The settings repository instance.
     */
    protected $unitrepo;

    public function __construct(ItemRepository $itemrepo, UserRepository $userrepo, UnitRepository $unitrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('itemsMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
        ]);

        $this->middleware('itemsMiddlewareEdit')->only([
            'edit',
            'update',
        ]);

        $this->middleware('itemsMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('itemsMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('itemsMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);

        //repos
        $this->itemrepo = $itemrepo;
        $this->userrepo = $userrepo;
        $this->unitrepo = $unitrepo;
    }

    /**
     * Display a listing of items
     * @param object CategoryRepository instance of the repository
     * @param object Category instance of the repository
     * @return blade view | ajax view
     */
    public function index(CategoryRepository $categoryrepo, Category $categorymodel) {

        //get items
        $items = $this->itemrepo->search();

        //get all categories (type: item) - for filter panel
        $categories = $categoryrepo->get('item');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('items'),
            'items' => $items,
            'count' => $items->count(),
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new item
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //client categories
        $categories = $categoryrepo->get('item');

        //units
        $units = $this->unitrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
            'units' => $units,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created itemin storage.
     * @param object ItemStoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(ItemStoreUpdate $request, UnitRepository $unitrepo) {

        //create the item
        if (!$item_id = $this->itemrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the item object (friendly for rendering in blade template)
        $items = $this->itemrepo->search($item_id);

        //update units list
        $unitrepo->updateList(request('item_unit'));

        //counting rows
        $rows = $this->itemrepo->search();
        $count = $rows->total();

        //reponse payload
        $payload = [
            'items' => $items,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified item
     * @param int $id item id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified item
     * @param object CategoryRepository instance of the repository
     * @param int $id item id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the item
        $item = $this->itemrepo->search($id);

        //client categories
        $categories = $categoryrepo->get('item');

        //units
        $units = $this->unitrepo->search();

        //not found
        if (!$item = $item->first()) {
            abort(409, __('lang.product_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'item' => $item,
            'categories' => $categories,
            'units' => $units,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified itemin storage.
     * @param object ItemStoreUpdate instance of the repository
     * @param object UnitRepository instance of the repository
     * @param int $id item id
     * @return \Illuminate\Http\Response
     */
    public function update(ItemStoreUpdate $request, UnitRepository $unitrepo, $id) {

        //update
        if (!$this->itemrepo->update($id)) {
            abort(409);
        }

        //get item
        $items = $this->itemrepo->search($id);

        //update units list
        $unitrepo->updateList(request('item_unit'));

        //reponse payload
        $payload = [
            'items' => $items,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified item from storage.
     * @return \Illuminate\Http\Response
     */
    public function destroy() {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //get the item
                $item = \App\Models\Item::Where('item_id', $id)->first();
                //delete client
                $item->delete();
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
     * Bulk change category for items
     * @url baseusr/items/bulkdelete
     * @return \Illuminate\Http\Response
     */
    public function bulkDelete() {

        //validation - post
        if (!is_array(request('item'))) {
            abort(409);
        }

        //loop through and delete each one
        $deleted = 0;
        foreach (request('item') as $item_id => $value) {
            if ($value == 'on') {
                //get the item
                if ($items = $this->itemrepo->search($item_id)) {
                    //remove the item
                    $items->first()->delete();
                    //hide and remove row
                    $jsondata['dom_visibility'][] = array(
                        'selector' => '#item_' . $item_id,
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
     * Show the form for updating the item
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all item categories
        $categories = $categoryrepo->get('item');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the item
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'item')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each item
        $allrows = array();
        foreach (request('ids') as $item_id => $value) {
            if ($value == 'on') {
                $item = \App\Models\Item::Where('item_id', $item_id)->first();
                //update the category
                $item->item_categoryid = request('category');
                $item->save();
                //get the item in rendering friendly format
                $items = $this->itemrepo->search($item_id);
                //add to array
                $allrows[] = $items;
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
                __('lang.products'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'items',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_sales' => 'active',
            'mainmenu_products' => 'active',
            'submenu_products' => 'active',
            'sidepanel_id' => 'sidepanel-filter-items',
            'dynamic_search_url' => url('items/search?action=search&itemresource_id=' . request('itemresource_id') . '&itemresource_type=' . request('itemresource_type')),
            'add_button_classes' => 'add-edit-item-button',
            'load_more_button_route' => 'items',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_product'),
            'add_modal_create_url' => url('items/create?itemresource_id=' . request('itemresource_id') . '&itemresource_type=' . request('itemresource_type')),
            'add_modal_action_url' => url('items?itemresource_id=' . request('itemresource_id') . '&itemresource_type=' . request('itemresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //items list page
        if ($section == 'items') {
            $page += [
                'meta_title' => __('lang.products'),
                'heading' => __('lang.products'),
                'sidepanel_id' => 'sidepanel-filter-items',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            if(request('itemresource_type') == 'invoice'){
                $page['dynamic_search_url'] = url('items/search?action=search&itemresource_type=invoice');
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