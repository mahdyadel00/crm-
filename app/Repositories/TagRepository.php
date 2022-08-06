<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for tags
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Facades\Schema;
use Log;

class TagRepository {

    /**
     * The tags repository instance.
     */
    protected $tags;

    /**
     * Inject dependecies
     */
    public function __construct(Tag $tags) {
        $this->tags = $tags;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object tag collection
     */
    public function search($id = '') {

        $tags = $this->tags->newQuery();

        // all client fields
        $tags->selectRaw('*');

        //joins
        $tags->leftJoin('users', 'users.id', '=', 'tags.tag_creatorid');

        //default where
        $tags->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_tag_id')) {
            $tags->where('tag_id', request('filter_tag_id'));
        }
        if (is_numeric($id)) {
            $tags->where('tag_id', $id);
        }

        //filter: title
        if (request()->filled('filter_tag_title')) {
            $tags->where('tag_title', request('filter_tag_title'));
        }

        //filter: resource type
        if (request()->filled('filter_tagresource_type')) {
            $tags->where('tagresource_type', request('filter_tagresource_type'));
        }

        //filter: resource id
        if (request()->filled('filter_tagresource_id')) {
            $tags->where('tagresource_id', request('filter_tagresource_id'));
        }
        //filter: date created (start)
        if (request()->filled('filter_tag_created_start')) {
            $tags->whereDate('tag_created', '>=', request('filter_tag_created_start'));
        }

        //filter: date created (end)
        if (request()->filled('filter_tag_created_end')) {
            $tags->whereDate('tag_created', '<=', request('filter_tag_created_end'));
        }

        //filter created by
        if (is_array(request('filter_tag_creatorid')) && !empty(array_filter(request('filter_tag_creatorid')))) {
            $tags->whereIn('tag_creatorid', request('filter_tag_creatorid'));
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('tags', request('orderby'))) {
                $tags->orderBy(request('orderby'), request('sortorder'));
            }
            switch (request('orderby')) {
            case 'created_by':
                $tags->orderBy('first_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $tags->orderBy('tag_id', 'desc');
        }

        // Get the results and return them.
        return $tags->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Bulk delete tags
     * @param string $ref_type type of tags. e.g. client|project etd
     * @param int $ref_id the id of the resource
     * @return bool
     */
    public function delete($ref_type = '', $ref_id = '') {

        //validations
        if ($ref_type == '' || !is_numeric($ref_id)) {
            return false;
        }

        $tags = $this->tags->newQuery();
        $tags->where('tagresource_type', '=', $ref_type);
        $tags->where('tagresource_id', '=', $ref_id);
        $tags->delete();

    }

    /**
     * get tags by type
     *     - this will fetch tags created by a given user or public tags
     *     - other users tags will not be fetched
     *     - this is normally using for creating a new resource and so no need to show other users tags
     * @param string $type type of tags. e.g. client|project etc
     * @return object
     */
    public function getByType($type = '') {

        //validations
        if ($type == '') {
            Log::error("record could not be updated - database error", ['process' => '[TagRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        $query = $this->tags->newQuery();
        $query->where('tagresource_type', '=', $type);

        //do not show other user's tags in dropdown lists
        $query->where(function ($q) {
            $q->where('tag_visibility', '=', 'user');
            $q->where('tag_creatorid', '=', auth()->id());
            $q->orWhere('tag_visibility', '=', 'public');
        });

        //group duplicates
        $query->groupBy('tag_title');
        return $query->get();
    }

    /**
     * get tags by resource type and id
     *       - this will load all tags that are attributed to a resource
     *       - including tags that were not created by the user
     *       - this is normally used when editing a resource and we want all tags to show
     * @param string $type type of tags. e.g. client|project etc
     * @param numeric $id the id of the resource
     * @return object
     */
    public function getByResource($type = '', $id = '') {

        //validations
        if ($type == '' || !is_numeric($id)) {
            return false;
        }

        $query = $this->tags->newQuery();
        $query->where('tagresource_type', '=', $type);
        $query->where('tagresource_id', '=', $id);

        //group duplicates
        $query->groupBy('tag_title');
        return $query->get();
    }

    /**
     * Bulk add tags
     * @param string $ref_type type of tags. e.g. client|project etd
     * @param string $visibility was the tag created by admin (public) or by user. public|user
     * @param int $ref_id the id of the resource
     * @return bool
     */
    public function add($ref_type = '', $ref_id = '', $visibility = 'user') {

        //validations
        if ($ref_type == '' || !is_numeric($ref_id)) {
            Log::error("validation error - invalid params", ['process' => '[TagsRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //add each one
        if (request()->filled('tags')) {
            foreach (request('tags') as $newtag) {
                //clean the tag
                $newtag = cleanTag($newtag);
                //store
                $tag = new $this->tags;
                $tag->tagresource_type = $ref_type;
                $tag->tagresource_id = $ref_id;
                $tag->tag_creatorid = Auth()->user()->id;
                $tag->tag_title = $newtag;
                $tag->tag_visibility = $visibility;
                $tag->save();
            }
        }

    }

    /**
     * various feeds for ajax auto complete
     * @param string $type (team|client)
     * @param string $searchterm
     * @return array
     */
    public function autocompleteFeed($type = '', $searchterm = '') {

        //validation
        if ($searchterm == '') {
            return [];
        }

        //start
        $query = $this->tags->newQuery();
        $query->selectRaw("tag_title AS value, tag_id AS id");

        //filter
        if ($type != '') {
            $query->where('tagresource_type', '=', $type);
        }

        $query->where('tag_title', 'like', '%' . $searchterm . '%');

        //return
        return $query->get();
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $tag = new $this->tags;

        //data
        $tag->tag_title = request('tag_title');
        $tag->tag_creatorid = auth()->id();
        $tag->tag_visibility = request('tag_visibility');
        $tag->tagresource_type = request('tagresource_type');
        $tag->tagresource_id = 0;

        //save and return id
        if ($tag->save()) {
            return $tag->tag_id;
        } else {
            Log::error("record could not be created - database error", ['process' => '[TagsRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed bool or id of record
     */
    public function update($id) {

        //get the record
        if (!$tag = $this->tags->find($id)) {
            return false;
        }

        //general
        $tag->tag_title = request('tag_title');

        //save
        if ($tag->save()) {
            return $tag->tag_id;
        } else {
            Log::error("record could not be updated - database error", ['process' => '[TagsRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}