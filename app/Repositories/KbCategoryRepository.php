<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for kb categories
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\KbCategories;

class KbCategoryRepository {

    /**
     * The category model instance.
     */
    protected $category;

    /**
     * Inject dependecies
     */
    public function __construct(KbCategories $category) {
        $this->category = $category;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object kb category collection
     */
    public function search($id = '') {

        $categories = $this->category->newQuery();

        //joins
        $categories->leftJoin('users', 'users.id', '=', 'kb_categories.kbcategory_creatorid');

        // all client fields
        $categories->selectRaw('*');

        //count articles
        $categories->selectRaw('(SELECT COUNT(*)
                                      FROM knowledgebase
                                      WHERE knowledgebase_categoryid = kb_categories.kbcategory_id)
                                      AS count_articles');

        if (is_numeric($id)) {
            $categories->where('kbcategory_id', $id);
        }

        //filter team user
        if (request()->filled('filter_user_type') && request('filter_user_type') == 'team') {
            $categories->WhereIn('kbcategory_visibility', ['everyone', 'team']);
        }

        //filter client user
        if (request()->filled('filter_user_type') && request('filter_user_type') == 'client') {
            $categories->WhereIn('kbcategory_visibility', ['everyone', 'client']);
        }

        //default sorting
        $categories->orderBy('kbcategory_position', 'asc');

        // Get the results and return them.
        return $categories->paginate(10000);
    }

    /**
     * Create a new record
     * @param int $position new position
     * @return mixed int|bool
     */
    public function create($position = '') {

        //validate
        if (!is_numeric($position)) {
            Log::error("validation error - invalid params", ['process' => '[KbCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //save
        $category = new $this->category;

        //data
        $category->kbcategory_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('kbcategory_title'));
        $category->kbcategory_description = request('kbcategory_description');
        $category->kbcategory_icon = request('kbcategory_icon');
        $category->kbcategory_type = request('kbcategory_type');
        $category->kbcategory_visibility = request('kbcategory_visibility');
        $category->kbcategory_creatorid = auth()->id();
        $category->kbcategory_position = $position;

        //save and return id
        if ($category->save()) {
            //update slug
            $category->kbcategory_slug = createSlug($category->kbcategory_id, $category->kbcategory_title);
            $category->save();
            //return id
            return $category->kbcategory_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[KbCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if (!$category = $this->category->find($id)) {
            return false;
        }

        //general
        $category->kbcategory_title = preg_replace('%[\[\'"\/\?\\\{}\]]%', '', request('kbcategory_title'));
        $category->kbcategory_description = request('kbcategory_description');
        $category->kbcategory_icon = request('kbcategory_icon');
        $category->kbcategory_visibility = request('kbcategory_visibility');
        $category->kbcategory_slug = createSlug($id, $category->kbcategory_title);

        //save
        if ($category->save()) {
            return $category->kbcategory_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[KbCategoryRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}