<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for knowledgebase
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\knowledgebase\CreateResponse;
use App\Http\Responses\knowledgebase\DestroyResponse;
use App\Http\Responses\knowledgebase\EditResponse;
use App\Http\Responses\knowledgebase\IndexResponse;
use App\Http\Responses\knowledgebase\ShowResponse;
use App\Http\Responses\knowledgebase\StoreResponse;
use App\Http\Responses\knowledgebase\UpdateResponse;
use App\Repositories\KbCategoryRepository;
use App\Repositories\KnowledgebaseRepository;
use App\Rules\NoTags;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Knowledgebase extends Controller {

    /**
     * The knowledgebase repository instance.
     */
    protected $kbrepo;

    /**
     * The category repository instance.
     */
    protected $categoryrepo;

    public function __construct(KnowledgebaseRepository $kbrepo, KbCategoryRepository $categoryrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //route middleware
        $this->middleware('knowledgebaseMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('knowledgebaseMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('knowledgebaseMiddlewareEdit')->only([
            'edit',
            'update',
        ]);

        $this->middleware('knowledgebaseMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->middleware('knowledgebaseMiddlewareShow')->only([
            'show',
        ]);

        $this->kbrepo = $kbrepo;

        $this->categoryrepo = $categoryrepo;
    }

    /**
     * Display a listing of kb
     * @return blade view | ajax view
     */
    public function index() {

        //default
        $active_category = '';
        $active_category_slug = '';

        //validation
        if (request()->segment(3) == '') {
            abort(404);
        }

        //category ajax settings if the request had a category id
        if ($category = \App\Models\KbCategories::Where('kbcategory_slug', request()->segment(3))->first()) {
            $active_category = $category->kbcategory_title;
            $active_category_slug = $category->kbcategory_slug;
            request()->merge([
                'filter_category_id' => $category->kbcategory_id,
            ]);
        }

        //get articles
        $knowledgebase = $this->kbrepo->search();

        //get knowledgebase categories
        $categories = $this->categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('kb', $category),
            'knowledgebase' => $knowledgebase,
            'categories' => $categories,
            'category' => $category,
            'active_category' => $active_category,
            'active_category_slug' => $active_category_slug,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new article
     * @return \Illuminate\Http\Response
     */
    public function create() {

        //categories
        $categories = $this->categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created article in storage.
     * @return \Illuminate\Http\Response
     */
    public function store() {

        //custom error messages
        $messages = [
            'knowledgebase_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'knowledgebase_title' => [
                'required',
                new NoTags,
            ],
            'knowledgebase_categoryid' => [
                'required',
                Rule::exists('kb_categories', 'kbcategory_id'),
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

        //get the category
        $category = \App\Models\KbCategories::Where('kbcategory_id', request('knowledgebase_categoryid'))->first();

        //validate
        if ($category->kbcategory_type == 'text') {
            if (!request()->filled('knowledgebase_text')) {
                abort(409, __('lang.description') . ' - ' . __('lang.is_required'));
            }
        }

        //validate embed code
        if ($category->kbcategory_type == 'video') {
            //the youtube embed code
            $embed_code = trim(request('knowledgebase_embed_code'));
            //empty
            if (!request()->filled('knowledgebase_embed_code')) {
                abort(409, __('lang.youtube_embed_code') . ' - ' . __('lang.is_required'));
            }

            //ensure it looks like a valie embed code (BNS Regex - 22 Aug 2021)
            if (!preg_match('%^<iframe width="[1-9][0-9][0-9]".height="[1-9][0-9][0-9].*src="https://.*youtube.*/embed/.*</iframe>$%', $embed_code)) {
                abort(409, __('lang.invalid_youtube_embed_code'));
            }

            //get video id from the embed code (BNS Regex - 22 Aug 2021)
            if (preg_match('%^<iframe width="[1-9][0-9][0-9]".height="[1-9][0-9][0-9].*src="https://.*youtube.*/embed/(.*?)".*</iframe>$%', $embed_code, $regs)) {
                $video_id = $regs[1];
                if ($video_id == '') {
                    abort(409, __('lang.error_processing_embed_code'));
                }
            } else {
                abort(409, __('lang.error_processing_embed_code'));
            }

            //merge video data
            request()->merge([
                'knowledgebase_embed_video_id' => $video_id,
                'knowledgebase_embed_thumb' => 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg',
            ]);
        }

        //create the foo
        if (!$knowledgebase_id = $this->kbrepo->create()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get tge article
        $article = \App\Models\Knowledgebase::Where('knowledgebase_id', $knowledgebase_id)->first();

        //get all articles
        request()->merge([
            'filter_category_id' => request('knowledgebase_categoryid'),
        ]);
        $knowledgebase = $this->kbrepo->search();

        //reponse payload
        $payload = [
            'knowledgebase' => $knowledgebase,
            'count' => $knowledgebase->count(),
            'article' => $article
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified article
     * @param int $id article id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get article
        $knowledgebase = \App\Models\Knowledgebase::Where('knowledgebase_slug', request('knowledgebase_slug'))->first();

        //get this category
        $category = \App\Models\KbCategories::Where('kbcategory_id', $knowledgebase->knowledgebase_categoryid)->first();

        //get knowledgebase categories
        $categories = $this->categoryrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('show', ['category_title' => $category->kbcategory_title]),
            'categories' => $categories,
            'knowledgebase' => $knowledgebase,
            'category' => $category,
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing the specified article
     * @param int $id article id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        //get the foo
        $knowledgebase = $this->kbrepo->search($id);

        //not found
        if (!$knowledgebase = $knowledgebase->first()) {
            abort(409, __('lang.knowledgebase_not_found'));
        }

        //get knowledgebase categories
        $categories = $this->categoryrepo->search();
        //some required data
        request()->merge([
            'knowledgebase_categoryid' => $knowledgebase->knowledgebase_categoryid,
            'category_type' => $knowledgebase->kbcategory_type,
        ]);
        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'knowledgebase' => $knowledgebase,
            'categories' => $categories,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified article in storage.
     * @param int $id article id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [
            'knowledgebase_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'knowledgebase_title' => [
                'required',
                new NoTags,
            ],
            'knowledgebase_categoryid' => [
                'required',
                Rule::exists('kb_categories', 'kbcategory_id'),
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

        if (!$article = \App\Models\Knowledgebase::Where('knowledgebase_id', $id)->first()) {
            abort(404);
        }

        //get the category
        if (!$category = \App\Models\KbCategories::Where('kbcategory_id', $article->knowledgebase_categoryid)->first()) {
            abort(409);
        }

        //validate
        if ($category->kbcategory_type == 'text') {
            if (!request()->filled('knowledgebase_text')) {
                abort(409, __('lang.description') . ' - ' . __('lang.is_required'));
            }
        }

        //validate embed code
        if ($category->kbcategory_type == 'video') {
            //the youtube embed code
            $embed_code = trim(request('knowledgebase_embed_code'));
            //empty
            if (!request()->filled('knowledgebase_embed_code')) {
                abort(409, __('lang.youtube_embed_code') . ' - ' . __('lang.is_required'));
            }

            //ensure it looks like a valie embed code (BNS Regex - 22 Aug 2021)
            if (!preg_match('%^<iframe width="[1-9][0-9][0-9]".height="[1-9][0-9][0-9].*src="https://.*youtube.*/embed/.*</iframe>$%', $embed_code)) {
                abort(409, __('lang.invalid_youtube_embed_code'));
            }

            //get video id from the embed code (BNS Regex - 22 Aug 2021)
            if (preg_match('%^<iframe width="[1-9][0-9][0-9]".height="[1-9][0-9][0-9].*src="https://.*youtube.*/embed/(.*?)".*</iframe>$%', $embed_code, $regs)) {
                $video_id = $regs[1];
                if ($video_id == '') {
                    abort(409, __('lang.error_processing_embed_code'));
                }
            } else {
                abort(409, __('lang.error_processing_embed_code'));
            }

            //merge video data
            request()->merge([
                'knowledgebase_embed_video_id' => $video_id,
                'knowledgebase_embed_thumb' => 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg',
            ]);
        }

        //update article
        if (!$this->kbrepo->update($id)) {
            abort(409);
        }

        //get all articles
        request()->merge([
            'filter_category_id' => request('knowledgebase_categoryid'),
        ]);
        $knowledgebase = $this->kbrepo->search();

        //reponse payload
        $payload = [
            'category' => $category,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified article from storage.
     * @param int $id article id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //get the item
        if (!$knowledgebase = $this->kbrepo->search($id)) {
            abort(409);
        }

        //get the category
        $article = $knowledgebase->first();
        if (!$category = \App\Models\KbCategories::Where('kbcategory_id', $article->knowledgebase_categoryid)->first()) {
            abort(409);
        }

        //remove the item
        $knowledgebase->first()->delete();

        //reponse payload
        $payload = [
            'category' => $category,
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
                __('lang.knowledgebase'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'kb',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_kb' => 'active',
            'sidepanel_id' => 'sidepanel-filter-kb',
            'dynamic_search_url' => url('kb/search?action=search&knowledgebaseresource_id=' . request('category_slug')),
            'add_button_classes' => 'add-edit-foo-button',
            'load_more_button_route' => 'kb',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_article'),
            'add_modal_create_url' => url('kb/create'),
            'add_modal_action_url' => url('kb'),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //kb list page
        if ($section == 'kb') {
            $page += [
                'meta_title' => __('lang.knowledgebase'),
                'heading' => __('lang.knowledgebase'),
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            $page['crumbs'] = [
                __('lang.knowledgebase'),
                $data->kbcategory_title,
            ];
            $page['add_modal_create_url'] = url('kb/create?action=new_article&knowledgebase_categoryid=' . $data->kbcategory_id . '&category_type=' . $data->kbcategory_type);
            $page['add_modal_action_url'] = url('kb?action=new_article&knowledgebase_categoryid=' . $data->kbcategory_id . '&category_type=' . $data->kbcategory_type);

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

        //edit new resource
        if ($section == 'show') {
            $page += [
                'meta_title' => __('lang.knowledgebase'),
                'heading' => __('lang.knowledgebase'),
            ];
            $page['crumbs'] = [
                __('lang.knowledgebase'),
                $data['category_title'],
            ];
            return $page;
        }

        //return
        return $page;
    }
}