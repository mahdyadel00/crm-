<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Templates;

use App\Http\Controllers\Controller;
use App\Http\Responses\Templates\Projects\CreateResponse;
use App\Http\Responses\Templates\Projects\DestroyResponse;
use App\Http\Responses\Templates\Projects\DetailsResponse;
use App\Http\Responses\Templates\Projects\EditResponse;
use App\Http\Responses\Templates\Projects\IndexResponse;
use App\Http\Responses\Templates\Projects\ShowDynamicResponse;
use App\Http\Responses\Templates\Projects\ShowResponse;
use App\Http\Responses\Templates\Projects\StoreResponse;
use App\Http\Responses\Templates\Projects\UpdateDetailsResponse;
use App\Http\Responses\Templates\Projects\UpdateResponse;
use App\Repositories\CustomFieldsRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\MilestoneCategoryRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Projects extends Controller {

    /**
     * The project repository instance.
     */
    protected $projectrepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

        /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    public function __construct(
        ProjectRepository $projectrepo,
        TagRepository $tagrepo,
        CustomFieldsRepository $customrepo
    ) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //Permissions on methods
        $this->middleware('projectTemplatesMiddlewareIndex')->only([
            'index',
            'update',
            'store',
        ]);

        $this->middleware('projectTemplatesMiddlewareShow')->only([
            'show',
            'showDynamic',
            'details',
        ]);

        $this->middleware('projectTemplatesMiddlewareEdit')->only([
            'edit',
            'update',
            'updateDescription',
        ]);

        $this->middleware('projectTemplatesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('projectTemplatesMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->projectrepo = $projectrepo;
        $this->tagrepo = $tagrepo;
        $this->customrepo = $customrepo;

    }

    /**
     * Display a listing of projects
     * @url baseusr/projects?page=1&source=ext&action=load
     * @urlquery
     *    - [page] numeric|null (pagination page number)
     *    - [source] ext|null  (ext: when called from embedded pages)
     *    - [action] load | null (load: when making additional ajax calls)
     * @return blade view | ajax view
     */
    public function index() {

        //get team members
        $projects = $this->projectrepo->search();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'projects' => $projects,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //get all categories (type: project) - for filter panel
        $categories = $categoryrepo->get('project');

        //project custom fields

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'fields' => $this->getCustomFields(),
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created resource in storage.
     * @return \Illuminate\Http\Response
     */
    public function store(
        MilestoneCategoryRepository $milestonecategories,
        MilestoneRepository $milestonerepo) {

        //validate
        $validator = Validator::make(request()->all(), [
            'project_title' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //create the project
        if (!$project_id = $this->projectrepo->createTemplate()) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //get the project object (friendly for rendering in blade template)
        $projects = $this->projectrepo->search($project_id, ['apply_filters' => false]);
        $project = $projects->first();

        //add default project categories
        $position = $milestonecategories->addProjectMilestones($project);

        //create default/'uncategorised' milstone category
        $milestonerepo->addUncategorised($project_id, $position);

        //counting all rows
        $rows = $this->projectrepo->search();
        $count = $rows->count();

        //reponse payload
        $payload = [
            'id' => $project_id,
            'projects' => $projects,
            'count' => $count,
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified project
     * @param object TimerRepository instance of the repository
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get the project
        $projects = $this->projectrepo->search($id);

        //project
        $project = $projects->first();

        //set page
        $page = $this->pageSettings('project', $project);

        $page['dynamic_url'] = url('templates/projects/' . $project->project_id . '/project-details');

        //reponse payload
        $payload = [
            'page' => $page,
            'project' => $project,
            'fields' => $this->getCustomFields(),
        ];

        //response
        return new ShowResponse($payload);
    }

    /**
     * Display the specified project
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function showDynamic($id) {

        //get the project
        $projects = $this->projectrepo->search($id);

        //project
        $project = $projects->first();

        $page = $this->pageSettings('project', $project);

        //set dynamic url for use in template
        switch (request()->segment(4)) {
        case 'tasks':
            $page['dynamic_url'] = url('tasks?source=ext&filter_pt=template&taskresource_type=project&taskresource_id=' . $project->project_id);
            break;
        case 'files':
            $page['dynamic_url'] = url('files?source=ext&filter_pt=template&fileresource_type=project&fileresource_id=' . $project->project_id);
            break;
        case 'milestones':
            $page['dynamic_url'] = url('milestones?source=ext&filter_pt=template&milestoneresource_type=project&milestoneresource_id=' . $project->project_id);
            break;
        case 'details':
            $page['dynamic_url'] = url('templates/projects/' . $project->project_id . '/project-details');
            break;
        default:
            $page['dynamic_url'] = url('templates/projects/' . $project->project_id . '/project-details');
            break;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'project' => $project,
        ];

        //response
        return new ShowDynamicResponse($payload);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id resource id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the project
        $project = $this->projectrepo->search($id);

        //not found
        if (!$project = $project->first()) {
            abort(404);
        }

        //client categories
        $categories = $categoryrepo->get('project');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'project' => $project,
            'categories' => $categories,
            'fields' => $this->getCustomFields($project),
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

        //validate
        $validator = Validator::make(request()->all(), [
            'project_title' => 'required',
            'project_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
        ]);

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
        if (!$this->projectrepo->updateTemplate($id)) {
            abort(409);
        }

        //get project
        $projects = $this->projectrepo->search($id);

        //reponse payload
        $payload = [
            'projects' => $projects,
            'id' => $id,
        ];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Remove the specified project from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo, $id) {

        //delete each record in the array
        $destroyrepo->destroyProject($id);

        //reponse payload
        $payload = [
            'id' => $id,
        ];

        //generate a response
        return new DestroyResponse($payload);

    }

    /**
     * Return ajax details for project
     * @param int $id project id
     * @return \Illuminate\Http\Response
     */
    public function details($id) {

        //get the project
        $project = $this->projectrepo->search($id);

        //get tags
        $tags = $this->tagrepo->getByResource('project', $id);

        //not found
        if (!$project = $project->first()) {
            abort(409, __('lang.project_not_found'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('project', $project),
            'project' => $project,
            'tags' => $tags,
        ];

        //response
        return new DetailsResponse($payload);
    }

    /**
     * update project description and also the tags
     * @return \Illuminate\Http\Response
     */
    public function updateDescription() {

        //get the project
        $project = \App\Models\Project::Where('project_id', request()->route('project'))->first();

        //update description
        $project->project_description = request('description');

        //save
        $project->save();

        //delete & update tags
        $this->tagrepo->delete('project', $project->project_id);
        $this->tagrepo->add('project', $project->project_id);

        //get tags
        $tags = $this->tagrepo->getByResource('project', $project->project_id);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('project', $project),
            'project' => $project,
            'tags' => $tags,
        ];

        //response
        return new UpdateDetailsResponse($payload);

    }

    /**
     * get all custom fields for clients
     *   - if they are being used in the 'edit' modal form, also get the current data
     *     from the cliet record. Store this temporarily in '$field->customfields_name'
     *     this will then be used to prefill data in the custom fields
     * @param model client model - only when showing the edit modal form
     * @return collection
     */
    public function getCustomFields($obj = '') {

        //set typs
        request()->merge([
            'customfields_type' => 'projects',
            'filter_show_standard_form_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fields
        $fields = $this->customrepo->search();

        //when in editing view - get current value that is stored for this custom field
        if ($obj instanceof \App\Models\Project) {
            foreach ($fields as $field) {
                $field->current_value = $obj[$field->customfields_name];
            }
        }

        return $fields;
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
                __('lang.templates'),
                __('lang.project_templates'),
            ],
            'meta_title' => __('lang.project_templates'),
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'projecttemplates',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_projects' => 'active',
            'submenu_templates' => 'active',
            'load_more_button_route' => 'templates/projects',
            'source' => 'list',
            'heading' => __('lang.templates'),
        ];

        //project page
        if ($section == 'project') {
            //adjust
            $page['page'] = 'project';

            $page['heading'] = $data->project_title;

            //add
            $page += [
                'crumbs_special_class' => 'main-pages-crumbs',
                'meta_title' => __('lang.project_templates') . ' - ' . $data->project_title,
                'project_id' => request()->segment(2),
                'source_for_filter_panels' => 'ext',
                'section' => 'overview',
            ];
            //ajax loading and tabs
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