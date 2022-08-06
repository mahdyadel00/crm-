<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for tags
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Tags\CreateResponse;
use App\Http\Responses\Tags\DestroyResponse;
use App\Http\Responses\Tags\EditResponse;
use App\Http\Responses\Tags\IndexResponse;
use App\Http\Responses\Tags\StoreResponse;
use App\Http\Responses\Tags\UpdateResponse;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Validator;

class Tags extends Controller {

    /**
     * array of valid category types.
     */
    protected $resource_types;

    public function __construct(TagRepository $tagrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //admin
        $this->middleware('adminCheck');

        $this->tagrepo = $tagrepo;

        //validate type (note: update this when you add new types)
        $this->resource_types = [
            'project',
            'client',
            'estimate',
            'ticket',
            'lead',
            'invoice',
            'contract',
            'tag',
            'item',
        ];
    }

    /**
     * Display a listing of tags
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //get tags
        $tags = $this->tagrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('tags'),
            'tags' => $tags,
            'resource_types' => $this->resource_types,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new tag
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'resource_types' => $this->resource_types,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created tag in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'tag_title' => 'required',
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

        //update the request object
        request()->merge([
            'tag_visibility' => 'public',
            'tagresource_id' => 0,
            'tag_title' => cleanTag(request('tag_title')),
        ]);

        //check duplicates (do this after cleaning the tag)
        if (\App\Models\Tag::where('tag_title', request('tag_title'))
            ->where('tagresource_type', request('tagresource_type'))
            ->where('tag_visibility', 'public')
            ->exists()) {
            abort(409, __('lang.tag_already_exists'));
        }

        //create the tag
        if (!$tag_id = $this->tagrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the tag object (friendly for rendering in blade template)
        $tags = $this->tagrepo->search($tag_id);

        //reponse payload
        $payload = [
            'tags' => $tags,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Show the form for editing the specified tag
     * @param int $id tag id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the tag
        $tags = $this->tagrepo->search($id);

        //not found
        if (!$tag = $tags->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'tag' => $tag,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified tag in storage.
     * @param int $id tag id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'tag_title' => 'required',
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

        //update the request object
        request()->merge([
            'tag_title' => cleanTag(request('tag_title')),
        ]);

        //check duplicates
        if (\App\Models\Tag::where('tag_title', request('tag_title'))
            ->where('tagresource_type', request('tagresource_type'))
            ->where('tag_visibility', 'public')
            ->where('tag_id', '!=', $id)
            ->exists()) {
            abort(409, __('lang.tag_already_exists'));
        }

        //update the tag
        if (!$this->tagrepo->update($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the tag object (friendly for rendering in blade template)
        $tags = $this->tagrepo->search($id);

        //reponse payload
        $payload = [
            'tags' => $tags,
        ];

        //process reponse
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified tag from storage.
     * @param int $id tag id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the item
        if (!$tags = $this->tagrepo->search($id)) {
            abort(409);
        }

        //remove the item
        $tags->first()->delete();

        //reponse payload
        $payload = [
            'tag_id' => $id,
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
                __('lang.tags'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'tags',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_tags' => 'active',
            'sidepanel_id' => 'sidepanel-filter-tags',
            'dynamic_search_url' => url('tags/search?action=search&tagresource_id=' . request('tagresource_id') . '&tagresource_type=' . request('tagresource_type')),
            'add_button_classes' => 'add-edit-tag-button',
            'load_more_button_route' => 'tags',
            'source' => 'list',
        ];

        config([
            //visibility - add project buttton
            //'visibility.list_page_actions_add_button' => true,
            //visibility - filter button
            'visibility.list_page_actions_filter_button' => true,
            //visibility - search field
            'visibility.list_page_actions_search' => false,
        ]);

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_tag'),
            'add_modal_create_url' => url('tags/create?tagresource_id=' . request('tagresource_id') . '&tagresource_type=' . request('tagresource_type')),
            'add_modal_action_url' => url('tags?tagresource_id=' . request('tagresource_id') . '&tagresource_type=' . request('tagresource_type')),
            'add_modal_action_ajax_class' => 'js-ajax-ux-request',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //tags list page
        if ($section == 'tags') {
            $page += [
                'meta_title' => __('lang.tags'),
                'heading' => __('lang.tags'),
                'sidepanel_id' => 'sidepanel-filter-tags',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //ext page settings
        if ($section == 'ext') {
            $page += [
                'list_page_actions_size' => 'col-lg-12',

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
