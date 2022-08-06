<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Http\Request;
use Log;

class CategoryRepository {

    /**
     * The category repository instance.
     */
    protected $categories;

    /**
     * Inject dependecies
     */
    public function __construct(Category $categories) {
        $this->categories = $categories;
    }

    /**
     * get all categories on a given type
     * @param string $type the type of the category
     * @return object
     */
    public function get($type = '') {

        //new object
        $query = $this->categories->newQuery();

        //type
        if ($type != '') {
            $query->where('category_type', '=', $type);
        }

        //filters
        if (request('category_type') != '') {
            $query->where('category_type', '=', request('category_type'));
        }

        //get
        return $query->get();
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object category collection
     */
    public function search($id = '') {

        $categories = $this->categories->newQuery();

        //joins
        $categories->leftJoin('users', 'users.id', '=', 'categories.category_creatorid');

        // all client fields
        $categories->selectRaw('*');

        //count users
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM category_users
                                      WHERE categoryuser_categoryid = categories.category_id)
                                      AS count_users');
        //count clients
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM clients
                                      WHERE client_categoryid = categories.category_id)
                                      AS count_clients');
        //count projects
        $categories->selectRaw("(SELECT COUNT(*)
                                      FROM projects
                                      WHERE project_type = 'project'
                                      AND project_categoryid = categories.category_id)
                                      AS count_projects");
        //count expenses
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM expenses
                                      WHERE expense_categoryid = categories.category_id)
                                      AS count_expenses');
        //count invoices
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM invoices
                                      WHERE bill_categoryid = categories.category_id)
                                      AS count_invoices');
        //count leads
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM leads
                                      WHERE lead_categoryid = categories.category_id)
                                      AS count_leads');
        //count tickets
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM tickets
                                      WHERE ticket_categoryid = categories.category_id)
                                      AS count_tickets');
        //count items
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM items
                                      WHERE item_categoryid = categories.category_id)
                                      AS count_items');

        //count estimates
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM estimates
                                      WHERE bill_categoryid = categories.category_id)
                                      AS count_estimates');

        //count proposals
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_categoryid = categories.category_id)
                                      AS count_proposals');                                      

        //count contracts
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_categoryid = categories.category_id)
                                      AS count_contracts');                                      

                                      
        //default where
        $categories->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_category_id')) {
            $categories->where('category_id', request('filter_category_id'));
        }
        if (is_numeric($id)) {
            $categories->where('category_id', $id);
        }

        //filters: category_type
        if (request()->filled('filter_category_type')) {
            $categories->where('category_type', request('filter_category_type'));
        }

        //filters: visibility
        if (request()->filled('filter_category_visibility')) {
            $categories->where(function ($query) {
                $query->where('category_visibility', 'everyone');
                $query->orWhere('category_visibility', request('filter_category_visibility'));
            });
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('expenses', request('orderby'))) {
                $categories->orderBy(request('orderby'), request('sortorder'));
            }
        } else {
            //default sorting
            $categories->orderBy('category_name', 'asc');
        }

        // Get the results and return them.
        return $categories->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @return bool process outcome
     */
    public function create() {

        //save new user
        $category = new $this->categories;

        //data
        $category->category_name = request('category_name');
        $category->category_creatorid = auth()->id();
        $category->category_type = request('category_type');
        $category->category_visibility = request('category_visibility');
        $category->category_description = request('category_description');
        $category->category_icon = request('category_icon');

        //save and return id
        if ($category->save()) {
            //add slug
            $category->category_slug = createSlug($category->category_id, $category->category_name);
            $category->save();
            //return
            return $category->category_id;
        } else {
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$category = $this->categories->find($id)) {
            Log::error("updating record failed - record not found", ['process' => '[CategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //general
        $category->category_name = request('category_name');
        $category->category_description = request('category_description');
        if (request()->filled('category_visibility')) {
            $category->category_visibility = request('category_visibility');
        }
        if (request()->filled('category_description')) {
            $category->category_description = request('category_description');
        }
        if (request()->filled('category_icon')) {
            $category->category_icon = request('category_icon');
        }

        //save
        if ($category->save()) {
            //update slug
            $category->category_slug = createSlug($category->category_id, $category->category_name);
            $category->save();
            return $category->category_id;
        } else {
            Log::error("updating record failed -database error", ['process' => '[CategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}