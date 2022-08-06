<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for expenses
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expenses\ExpenseAttachProject;
use App\Http\Requests\Expenses\ExpenseStoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Expenses\AttachDettachResponse;
use App\Http\Responses\Expenses\attachDettachUpdateResponse;
use App\Http\Responses\Expenses\ChangeCategoryUpdateResponse;
use App\Http\Responses\Expenses\CreateResponse;
use App\Http\Responses\Expenses\DestroyResponse;
use App\Http\Responses\Expenses\EditResponse;
use App\Http\Responses\Expenses\IndexResponse;
use App\Http\Responses\Expenses\ShowResponse;
use App\Http\Responses\Expenses\StoreResponse;
use App\Http\Responses\Expenses\UpdateResponse;
use App\Http\Responses\Invoices\CreateResponse as CreateInvoiceResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\ExpenseRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Validator;

class Expenses extends Controller {

    /**
     * The expense repository instance.
     */
    protected $expenserepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The attachment repository instance.
     */
    protected $attachmentrepo;

    public function __construct(
        ExpenseRepository $expenserepo,
        UserRepository $userrepo,
        AttachmentRepository $attachmentrepo,
        TagRepository $tagrepo) {

        //parent
        parent::__construct();

        //middleware
        $this->middleware('auth');

        $this->middleware('expensesMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'attachDettachUpdate',
            'changeCategoryUpdate',
        ]);

        $this->middleware('expensesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('expensesMiddlewareShow')->only([
            'show',
        ]);

        $this->middleware('expensesMiddlewareEdit')->only([
            'edit',
            'update',
            'attachDettach',
            'attachDettachUpdate',
        ]);

        $this->middleware('expensesMiddlewareDestroy')->only([
            'destroy',
        ]);

        $this->middleware('expensesMiddlewareDownloadAttachment')->only([
            'downloadAttachment',
        ]);

        $this->middleware('expensesMiddlewareDeleteAttachment')->only([
            'deleteAttachment',
        ]);

        $this->middleware('expensesMiddlewareCreateInvoice')->only([
            'createNewInvoice',
        ]);

        //only needed for the [action] methods
        $this->middleware('expensesMiddlewareBulkEdit')->only(['changeCategoryUpdate']);

        $this->expenserepo = $expenserepo;

        $this->userrepo = $userrepo;

        $this->attachmentrepo = $attachmentrepo;

        $this->tagrepo = $tagrepo;
    }

    /**
     * Display a listing of expenses
     * @param object ProjectRepository instance of the repository
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index(ProjectRepository $projectrepo, CategoryRepository $categoryrepo) {

        //for fileter panel
        $projects = [];

        //basic page settings
        $page = $this->pageSettings('expenses');

        //get expense (paginated)
        $expenses = $this->expenserepo->search();

        //page setting for embedded view
        if (request('source') == 'ext') {
            $page = $this->pageSettings('ext');
        }

        //get all categories (type: expense) - for filter panel
        $categories = $categoryrepo->get('expense');

        //get clients project list
        if (config('visibility.filter_panel_clients_projects')) {
            if (is_numeric(request('expenseresource_id'))) {
                $projects = $projectrepo->search('', ['project_clientid' => request('expenseresource_id')]);
            }
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'expenses' => $expenses,
            'projects' => $projects,
            'count' => $expenses->count(),
            'stats' => $this->statsWidget(),
            'categories' => $categories,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new expense.
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //page settings
        $page = $this->pageSettings('create');

        //expense categories
        $categories = $categoryrepo->get('expense');

        //reponse payload
        $payload = [
            'page' => $page,
            'categories' => $categories,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created expense in storage.
     * @param object ExpenseStoreUpdate
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseStoreUpdate $request) {

        //for billable projects
        if (request('expense_billable') == 'on') {
            if (!$project = \App\Models\Project::Where('project_id', request('expense_projectid'))->first()) {
                abort(409, __('lang.project_not_found'));
            }
            request()->merge([
                'expense_clientid' => $project->project_clientid,
            ]);
        }

        //create the invoice
        if (!$expense_id = $this->expenserepo->create()) {
            abort(409);
        }

        //get the item object (friendly for rendering in blade template)
        $expenses = $this->expenserepo->search($expense_id, ['apply_filters' => false]);

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => request('expense_clientid'),
                    'attachmentresource_type' => 'expense',
                    'attachmentresource_id' => $expense_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //counting all rows (filtering depending on referral view)
        $data = ['apply_filters' => false];
        if (request()->filled('expenseresource_id') && request()->filled('expenseresource_type')) {
            $data = [
                'expenseresource_id' => request('expenseresource_id'),
                'expenseresource_type' => request('expenseresource_type'),
            ];
        }
        $rows = $this->expenserepo->search('', $data);
        $count = $rows->count();

        //reponse payload
        $payload = [
            'expenses' => $expenses,
            'expense' => $expenses->first(),
            'count' => $count,
            'stats' => $this->statsWidget(),
        ];

        //process reponse
        return new StoreResponse($payload);

    }

    /**
     * Display the specified expense.
     * @param int $id expense id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get the expense
        $expenses = $this->expenserepo->search($id);
        $expense = $expenses->first();

        //get attachment
        $attachments = \App\Models\Attachment::Where('attachmentresource_id', $id)
            ->Where('attachmentresource_type', 'expense')
            ->get();

        //reponse payload
        $payload = [
            'expense' => $expense,
            'attachments' => $attachments,
        ];

        //show the form
        return new ShowResponse($payload);
    }

    /**
     * Show the form for editing the specified expense.
     * @param object CategoryRepository instance of the repository
     * @param int $id expense id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //page settings
        $page = $this->pageSettings('edit');

        //get the expense
        $expense = $this->expenserepo->search($id);
        $expense = $expense->first();

        //client categories
        $categories = $categoryrepo->get('expense');

        //get attachment
        $attachments = \App\Models\Attachment::Where('attachmentresource_id', $id)
            ->Where('attachmentresource_type', 'expense')
            ->get();

        //reponse payload
        $payload = [
            'page' => $page,
            'expense' => $expense,
            'categories' => $categories,
            'attachments' => $attachments,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified expense in storage.
     * @param int $id expense id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {

        //custom error messages
        $messages = [
            'expense_categoryid.exists' => __('lang.item_not_found'),
        ];

        //validate
        $validator = Validator::make(request()->all(), [
            'expense_date' => 'required|date',
            'expense_amount' => 'required|numeric',
            'expense_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
            'expense_projectid' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value != '') {
                        if (!\App\Models\Project::find(request('expense_projectid'))) {
                            return $fail(__('lang.item_not_found'));
                        }
                    }
                },
            ],
            'expense_billable' => [
                'nullable',
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

        //for billable projects
        if (request('expense_billable') == 'on') {
            if (!$project = \App\Models\Project::Where('project_id', request('expense_projectid'))->first()) {
                abort(409, __('lang.project_not_found'));
            }

            //merge client id
            request()->merge([
                'expense_clientid' => $project->project_clientid,
            ]);
        }

        //update
        if (!$this->expenserepo->update($id)) {
            abort(409);
        }

        //get project
        $expenses = $this->expenserepo->search($id, ['apply_filters' => false]);
        $expense = $expenses->first();

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_clientid' => $expense->expense_clientid,
                    'attachmentresource_type' => 'expense',
                    'attachmentresource_id' => $expense->expense_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //reponse payload
        $payload = [
            'expenses' => $expenses,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new UpdateResponse($payload);

    }

    /**
     * Remove the specified expense from storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //delete
                $destroyrepo->destroyExpense($id);
                //add to array
                $allrows[] = $id;
            }
        }
        //reponse payload
        $payload = [
            'allrows' => $allrows,
            'stats' => $this->statsWidget(),
        ];

        //generate a response
        return new DestroyResponse($payload);
    }

    /**
     * download an expense attachment
     * @param int $id expense id
     * @return \Illuminate\Http\Response
     */
    public function downloadAttachment() {

        //check if file exists in the database
        $attachment = \App\Models\Attachment::Where('attachment_uniqiueid', request()->route('uniqueid'))->first();

        //confirm thumb exists
        if ($attachment->attachment_filename != '') {
            $file_path = "files/$attachment->attachment_directory/$attachment->attachment_filename";
            if (Storage::exists($file_path)) {
                return Storage::download($file_path);
            }
        }
        abort(404);
    }

    /**
     * show attach/dettach expense form
     * @return \Illuminate\Http\Response
     */
    public function attachDettach() {

        $expense = \App\Models\Expense::Where('expense_id', request('id'))->first();

        //reponse payload
        $payload = [
            'expense' => $expense,
        ];

        //show the form
        return new AttachDettachResponse($payload);
    }

    /**
     * attach or dettach client/project from expense
     * @param object ExpenseAttachProject instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function attachDettachUpdate(ExpenseAttachProject $request) {

        //get the expense
        $expense = \App\Models\Expense::Where('expense_id', request()->route('expense'))->first();

        //bug fix - set these as per post and not as per index middleware or filters
        request()->merge([
            'expense_clientid' => $_POST['expense_clientid'],
            'expense_projectid' => $_POST['expense_projectid'],
            'filter_expense_projectid' => $_POST['expense_clientid'],
        ]);

        //set values as posted
        $expense->expense_clientid = request('expense_clientid');
        $expense->expense_projectid = request('expense_projectid');

        //get the project
        if (request()->filled('expense_projectid')) {
            $project = \App\Models\Project::Where('project_id', request('expense_projectid'))->first();
            //sanity (ensure client is correct)
            $expense->expense_clientid = $project->project_clientid;
        }

        //save the new data
        $expense->save();

        //get expense (friendly)
        $expenses = $this->expenserepo->search(request()->route('expense'), ['apply_filters' => false]);

        //reponse payload
        $payload = [
            'expenses' => $expenses,
        ];

        //show the form
        return new attachDettachUpdateResponse($payload);

    }

    /**
     * download an expense attachment
     * @param int $id expense id
     * @return \Illuminate\Http\Response
     */
    public function deleteAttachment() {

        //check if file exists in the database
        $attachment = \App\Models\Attachment::Where('attachment_uniqiueid', request()->route('uniqueid'))->first();

        //confirm thumb exists
        if ($attachment->attachment_directory != '') {
            if (Storage::exists("files/$attachment->attachment_directory")) {
                Storage::deleteDirectory("files/$attachment->attachment_directory");
            }
        }

        $attachment->delete();

        //hide and remove row
        $jsondata['dom_visibility'][] = array(
            'selector' => '#expense_attachment_' . $attachment->attachment_id,
            'action' => 'slideup-slow-remove',
        );

        //response
        return response()->json($jsondata);
    }

    /**
     * Show the form for updating the expense.
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all expense categories
        $categories = $categoryrepo->get('expense');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the expense.
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'expense')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each expense
        $allrows = array();
        foreach (request('ids') as $expense_id => $value) {
            if ($value == 'on') {
                $expense = \App\Models\Expense::Where('expense_id', $expense_id)->first();
                //update the category
                $expense->expense_categoryid = request('category');
                $expense->save();
                //get the expense in rendering friendly format
                $expenses = $this->expenserepo->search($expense_id);
                //add to array
                $allrows[] = $expenses;
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
     * Show the form for creating a new expense.
     * @param object CategoryRepository instance of the repository
     * @param int $id client id
     * @return \Illuminate\Http\Response
     */
    public function createNewInvoice($id, CategoryRepository $categoryrepo) {

        //get the expense
        $expense = $this->expenserepo->search($id);
        $expense = $expense->first();

        //page settings
        $page = $this->pageSettings('create');

        //invoice categories
        $categories = $categoryrepo->get('invoice');

        //get tags
        $tags = $this->tagrepo->getByType('invoice');

        //reponse payload
        $payload = [
            'page' => $page,
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the form
        return new CreateInvoiceResponse($payload);
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
                __('lang.expenses'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'expenses',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_expenses' => 'active',
            'mainmenu_sales' => 'active',
            'submenu_expenses' => 'active',
            'sidepanel_id' => 'sidepanel-filter-expenses',
            'dynamic_search_url' => url('expenses/search?action=search&expenseresource_id=' . request('expenseresource_id') . '&expenseresource_type=' . request('expenseresource_type')),
            'add_button_classes' => 'add-edit-expense-button',
            'load_more_button_route' => 'expenses',
            'source' => 'list',
        ];

        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_expense'),
            'add_modal_create_url' => url('expenses/create?expenseresource_id=' . request('expenseresource_id') . '&expenseresource_type=' . request('expenseresource_type')),
            'add_modal_action_url' => url('expenses?expenseresource_id=' . request('expenseresource_id') . '&expenseresource_type=' . request('expenseresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //expenses list page
        if ($section == 'expenses') {
            $page += [
                'meta_title' => __('lang.expenses'),
                'heading' => __('lang.expenses'),
                'sidepanel_id' => 'sidepanel-filter-expenses',
            ];
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

    /**
     * data for the stats widget
     * @return array
     */
    private function statsWidget($data = array()) {

        //stats
        $count_all = $this->expenserepo->search('', ['stats' => 'count-all']);
        $sum_all = $this->expenserepo->search('', ['stats' => 'sum-all']);
        $sum_invoiced = $this->expenserepo->search('', ['stats' => 'sum-invoiced']);
        $sum_not_invoiced = $this->expenserepo->search('', ['stats' => 'sum-not-invoiced']);

        //default values
        $stats = [
            [
                'value' => $count_all,
                'title' => __('lang.count'),
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => runtimeMoneyFormat($sum_all),
                'title' => __('lang.total'),
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
            [
                'value' => runtimeMoneyFormat($sum_invoiced),
                'title' => __('lang.invoiced'),
                'percentage' => '100%',
                'color' => 'bg-warning',
            ],
            [
                'value' => runtimeMoneyFormat($sum_not_invoiced),
                'title' => __('lang.not_invoiced'),
                'percentage' => '100%',
                'color' => 'bg-danger',
            ],
        ];
        //return
        return $stats;
    }
}