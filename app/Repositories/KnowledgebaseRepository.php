<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Knowledgebase;
use Illuminate\Http\Request;

class KnowledgebaseRepository {

    /**
     * The knowledgebase repository instance.
     */
    protected $knowledgebase;

    /**
     * Inject dependecies
     */
    public function __construct(Knowledgebase $knowledgebase) {
        $this->knowledgebase = $knowledgebase;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object kb collection
     */
    public function search($id = '') {

        $knowledgebase = $this->knowledgebase->newQuery();

        // all client fields
        $knowledgebase->selectRaw('*');

        //joins
        $knowledgebase->leftJoin('users', 'users.id', '=', 'knowledgebase.knowledgebase_creatorid');
        $knowledgebase->leftJoin('categories', 'categories.category_id', '=', 'knowledgebase.knowledgebase_categoryid');
        $knowledgebase->leftJoin('kb_categories', 'kb_categories.kbcategory_id', '=', 'knowledgebase.knowledgebase_categoryid');

        //default where
        $knowledgebase->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_id')) {
            $knowledgebase->where('knowledgebase_id', request('filter_id'));
        }
        if (is_numeric($id)) {
            $knowledgebase->where('knowledgebase_id', $id);
        }

        //filter category
        if (request()->filled('filter_knowledgebase_categoryid')) {
            $knowledgebase->where('knowledgebase_categoryid', request('filter_knowledgebase_categoryid'));
        }

        //filter category slug
        if (request()->filled('filter_category_id')) {
            $knowledgebase->where('knowledgebase_categoryid', request('filter_category_id'));
        }

        //resource filtering
        if (request()->filled('knowledgebaseresource_id')) {
            $files->where('knowledgebase_slug', request('knowledgebaseresource_id'));
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $knowledgebase->where(function ($query) {
                $query->Where('knowledgebase_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('knowledgebase_text', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //default sorting
        if (config('system.settings_knowledgebase_article_ordering') == 'name-asc') {
            $knowledgebase->orderBy('knowledgebase_title', 'asc');
        }
        if (config('system.settings_knowledgebase_article_ordering') == 'name-desc') {
            $knowledgebase->orderBy('knowledgebase_title', 'desc');
        }
        if (config('system.settings_knowledgebase_article_ordering') == 'date-asc') {
            $knowledgebase->orderBy('knowledgebase_id', 'asc');
        }
        if (config('system.settings_knowledgebase_article_ordering') == 'date-desc') {
            $knowledgebase->orderBy('knowledgebase_id', 'desc');
        }

        // Get the results and return them.
        return $knowledgebase->paginate(100000000);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $knowledgebase = new $this->knowledgebase;

        //data
        $knowledgebase->knowledgebase_categoryid = request('knowledgebase_categoryid');
        $knowledgebase->knowledgebase_creatorid = auth()->id();
        $knowledgebase->knowledgebase_title = request('knowledgebase_title');
        $knowledgebase->knowledgebase_text = request('knowledgebase_text');
        $knowledgebase->knowledgebase_embed_video_id = request('knowledgebase_embed_video_id');
        $knowledgebase->knowledgebase_embed_code = request('knowledgebase_embed_code');
        $knowledgebase->knowledgebase_embed_thumb = request('knowledgebase_embed_thumb');

        //save and return id
        if ($knowledgebase->save()) {
            //add/update slug
            $knowledgebase->knowledgebase_slug = createSlug($knowledgebase->knowledgebase_id, $knowledgebase->knowledgebase_title);
            $knowledgebase->save();
            //return
            return $knowledgebase->knowledgebase_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[KnowledgebaseRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if (!$knowledgebase = $this->knowledgebase->find($id)) {
            return false;
        }

        //general
        $knowledgebase->knowledgebase_title = request('knowledgebase_title');
        $knowledgebase->knowledgebase_text = request('knowledgebase_text');
        $knowledgebase->knowledgebase_categoryid = request('knowledgebase_categoryid');
        $knowledgebase->knowledgebase_embed_video_id = request('knowledgebase_embed_video_id');
        $knowledgebase->knowledgebase_embed_code = request('knowledgebase_embed_code');
        $knowledgebase->knowledgebase_embed_thumb = request('knowledgebase_embed_thumb');

        //save
        if ($knowledgebase->save()) {
            //add/update slug
            $knowledgebase->knowledgebase_slug = createSlug($knowledgebase->knowledgebase_id, $knowledgebase->knowledgebase_title);
            $knowledgebase->save();
            //return
            return $knowledgebase->knowledgebase_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[KnowledgebaseRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}