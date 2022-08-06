<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoices\InvoiceClone;
use App\Http\Requests\Invoices\InvoiceRecurrringSettings;
use App\Http\Requests\Invoices\InvoiceSave;
use App\Http\Requests\Invoices\InvoiceStoreUpdate;
use App\Http\Responses\Common\ChangeCategoryResponse;
use App\Http\Responses\Invoices\AttachProjectResponse;
use App\Http\Responses\Invoices\ChangeCategoryUpdateResponse;
use App\Http\Responses\Invoices\CreateCloneResponse;
use App\Http\Responses\Invoices\CreateResponse;
use App\Http\Responses\Invoices\DestroyResponse;
use App\Http\Responses\Invoices\EditResponse;
use App\Http\Responses\Invoices\IndexResponse;
use App\Http\Responses\Invoices\PDFResponse;
use App\Http\Responses\Invoices\PublishResponse;
use App\Http\Responses\Invoices\RecurringSettingsResponse;
use App\Http\Responses\Invoices\ResendResponse;
use App\Http\Responses\Invoices\SaveResponse;
use App\Http\Responses\Invoices\ShowResponse;
use App\Http\Responses\Invoices\StoreCloneResponse;
use App\Http\Responses\Invoices\StoreResponse;
use App\Http\Responses\Invoices\UpdateResponse;
use App\Http\Responses\Pay\MolliePaymentResponse;
use App\Http\Responses\Pay\PaypalPaymentResponse;
use App\Http\Responses\Pay\RazorpayPaymentResponse;
use App\Http\Responses\Pay\StripePaymentResponse;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\CloneInvoiceRepository;
use App\Repositories\DestroyRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceGeneratorRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\LineitemRepository;
use App\Repositories\MolliePaymentRepository;
use App\Repositories\PaypalPaymentRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\RazorpayPaymentRepository;
use App\Repositories\StripePaymentRepository;
use App\Repositories\TagRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class Invoices extends Controller {

    /**
     * The invoice repository instance.
     */
    protected $invoicerepo;

    /**
     * The tags repository instance.
     */
    protected $tagrepo;

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The tax repository instance.
     */
    protected $taxrepo;

    /**
     * The unit repository instance.
     */
    protected $unitrepo;

    /**
     * The line item repository instance.
     */
    protected $lineitemrepo;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The emailer repository
     */
    protected $emailerrepo;

    /**
     * The invoice generator repository
     */
    protected $invoicegenerator;

    public function __construct(
        InvoiceRepository $invoicerepo,
        TagRepository $tagrepo,
        UserRepository $userrepo,
        TaxRepository $taxrepo,
        LineitemRepository $lineitemrepo,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        EmailerRepository $emailerrepo,
        InvoiceGeneratorRepository $invoicegenerator
    ) {

        //core controller instantation
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->middleware('invoicesMiddlewareIndex')->only([
            'index',
            'update',
            'store',
            'changeCategoryUpdate',
            'attachProjectUpdate',
            'dettachProject',
            'stopRecurring',
            'recurringSettingsUpdate',
        ]);

        $this->middleware('invoicesMiddlewareCreate')->only([
            'create',
            'store',
        ]);

        $this->middleware('invoicesMiddlewareEdit')->only([
            'edit',
            'update',
            'createClone',
            'storeClone',
            'stopRecurring',
            'dettachProject',
            'attachProject',
            'attachProjectUpdate',
            'emailClient',
            'saveInvoice',
            'recurringSettings',
            'recurringSettingsUpdate',
        ]);

        $this->middleware('invoicesMiddlewareShow')->only([
            'show',
            'paymentStripe',
            'downloadPDF',
        ]);

        $this->middleware('invoicesMiddlewareDestroy')->only([
            'destroy',
        ]);

        //only needed for the [action] methods
        $this->middleware('invoicesMiddlewareBulkEdit')->only([
            'changeCategoryUpdate',
        ]);

        $this->invoicerepo = $invoicerepo;
        $this->tagrepo = $tagrepo;
        $this->userrepo = $userrepo;
        $this->lineitemrepo = $lineitemrepo;
        $this->taxrepo = $taxrepo;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->invoicegenerator = $invoicegenerator;

    }

    /**
     * Display a listing of invoices
     * @param object ProjectRepository instance of the repository
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function index(ProjectRepository $projectrepo, CategoryRepository $categoryrepo) {

        //default
        $projects = [];

        //get invoices
        $invoices = $this->invoicerepo->search();

        //get all categories (type: invoice) - for filter panel
        $categories = $categoryrepo->get('invoice');

        //get all tags (type: lead) - for filter panel
        $tags = $this->tagrepo->getByType('invoice');

        //refresh invoices
        foreach ($invoices as $invoice) {
            $this->invoicerepo->refreshInvoice($invoice);
        }

        //get clients project list
        if (config('visibility.filter_panel_clients_projects')) {
            if (is_numeric(request('invoiceresource_id'))) {
                $projects = $projectrepo->search('', ['project_clientid' => request('invoiceresource_id')]);
            }
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('invoices'),
            'invoices' => $invoices,
            'projects' => $projects,
            'stats' => $this->statsWidget(),
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Show the form for creating a new invoice
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function createSelector() {

        $payload = [
            'title' => __('lang.add_invoice_splah_title'),

        ];

        //show the form
        return new CreateSelectorResponse($payload);
    }

    /**
     * Show the form for creating a new invoice
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function create(CategoryRepository $categoryrepo) {

        //invoice categories
        $categories = $categoryrepo->get('invoice');

        //get tags
        $tags = $this->tagrepo->getByType('invoice');

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('create'),
            'categories' => $categories,
            'tags' => $tags,
        ];

        //show the form
        return new CreateResponse($payload);
    }

    /**
     * Store a newly created invoicein storage.
     * @param object InvoiceStoreUpdate instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceStoreUpdate $request, ClientRepository $clientrepo) {

        //are we creating a new client
        if (request('client-selection-type') == 'new') {

            //create client
            if (!$client_id = $clientrepo->create([
                'send_email' => 'yes',
                'return' => 'id',
            ])) {
                abort(409);
            }

            //add client id to request
            request()->merge([
                'bill_clientid' => $client_id,
            ]);
        }

        //create the invoice
        if (!$bill_invoiceid = $this->invoicerepo->create()) {
            abort(409);
        }

        //add tags
        $this->tagrepo->add('invoice', $bill_invoiceid);

        //payloads - expense
        $this->expensesPayload($bill_invoiceid);

        //reponse payload
        $payload = [
            'id' => $bill_invoiceid,
        ];

        //process reponse
        return new StoreResponse($payload);
    }

    /**
     * Display the specified invoice
     *  [web preview example]
     *  http://example.com/invoices/29/pdf?view=preview
     * @param  int  $id invoice id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        //get invoice object payload
        if (!$payload = $this->invoicegenerator->generate($id)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //append to payload
        $payload['page'] = $this->pageSettings('invoice', $payload['bill']);

        //mark events as read
        \App\Models\EventTracking::where('parent_id', $id)
            ->where('parent_type', 'invoice')
            ->where('eventtracking_userid', auth()->id())
            ->update(['eventtracking_status' => 'read']);

        //if client - marked as opened
        if (auth()->user()->is_client) {
            \App\Models\Invoice::where('bill_invoiceid', $id)
                ->update(['bill_viewed_by_client' => 'yes']);
        }

        //custom fields
        $payload['customfields'] = \App\Models\CustomField::Where('customfields_type', 'clients')->get();

        //download pdf invoice
        if (request()->segment(3) == 'pdf') {
            //render view
            return new PDFResponse($payload);
        }

        //process reponse
        return new ShowResponse($payload);
    }

    /**
     * save invoice changes, when an ioice is being edited
     * @param object InvoiceSave instance of the request validation
     * @param int $id invoice id
     * @return array
     */
    public function saveInvoice(InvoiceSave $request, $id) {

        //get the invoice
        $invoices = $this->invoicerepo->search($id);
        $invoice = $invoices->first();

        //save each line item in the database
        $this->invoicerepo->saveLineItems($id, $invoice->bill_projectid);

        //update taxes
        $this->updateInvoiceTax($id);

        //update other invoice attributes
        $this->invoicerepo->updateInvoice($id);

        //reponse payload
        $payload = [
            'invoice' => $invoice,
        ];

        //response
        return new SaveResponse($payload);

    }

    /**
     * update the tax for an invoice
     * (1) delete existing invoice taxes
     * (2) for summary taxes - save new taxes
     * (3) [future]  - calculate and save line taxes (probably should just come from the frontend, same as summary taxes)
     * @param int $bill_invoiceid invoice id
     * @return array
     */
    private function updateInvoiceTax($bill_invoiceid = '') {

        //delete current invoice taxes
        \App\Models\Tax::Where('taxresource_type', 'invoice')
            ->where('taxresource_id', $bill_invoiceid)
            ->delete();

        //save taxes [summary taxes]
        if (is_array(request('bill_logic_taxes'))) {
            foreach (request('bill_logic_taxes') as $tax) {
                //get data elements
                $list = explode('|', $tax);
                $data = [
                    'tax_taxrateid' => $list[2],
                    'tax_name' => $list[1],
                    'tax_rate' => $list[0],
                    'taxresource_type' => 'invoice',
                    'taxresource_id' => $bill_invoiceid,
                ];
                $this->taxrepo->create($data);
            }
        }

    }

    /**
     * publish an invoice
     * @param int $id invoice id
     * @return \Illuminate\Http\Response
     */
    public function publishInvoice($id) {

        //generate the invoice
        if (!$payload = $this->invoicegenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //invoice
        $invoice = $payload['bill'];

        //validate current status
        if ($invoice->bill_status != 'draft') {
            abort(409, __('lang.invoice_already_piblished'));
        }

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $resource_id = (is_numeric($invoice->bill_projectid)) ? $invoice->bill_projectid : $invoice->bill_clientid;
        $resource_type = (is_numeric($invoice->bill_projectid)) ? 'project' : 'client';
        $data = [
            'event_creatorid' => auth()->id(),
            'event_item' => 'invoice',
            'event_item_id' => $invoice->bill_invoiceid,
            'event_item_lang' => 'event_created_invoice',
            'event_item_content' => __('lang.invoice') . ' - ' . $invoice->formatted_bill_invoiceid,
            'event_item_content2' => '',
            'event_parent_type' => 'invoice',
            'event_parent_id' => $invoice->bill_invoiceid,
            'event_parent_title' => $invoice->project_title,
            'event_clientid' => $invoice->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => $resource_type,
            'eventresource_id' => $resource_id,
            'event_notification_category' => 'notifications_billing_activity',

        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users (main client)
            $users = $this->userrepo->getClientUsers($invoice->bill_clientid, 'owner', 'ids');
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //other data
            $data = [];
            //send to client users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\PublishInvoice($user, $data, $invoice);
                    $mail->build();
                }
            }
        }

        //get invoice again
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $invoice->bill_invoiceid)->first();

        //get new invoice status and save it
        $bill_date = \Carbon\Carbon::parse($invoice->bill_date);
        $bill_due_date = \Carbon\Carbon::parse($invoice->bill_due_date);
        if ($bill_due_date->diffInDays(today(), false) < 0) {
            $invoice->bill_status = 'due';
        } else {
            $invoice->bill_status = 'overdue';
        }
        $invoice->save();

        //reponse payload
        $payload = [
            'invoice' => $invoice,
        ];

        //response
        return new PublishResponse($payload);
    }

    /**
     * email (resend) an invoice
     * @param int $id invoice id
     * @return \Illuminate\Http\Response
     */
    public function resendInvoice($id) {

        //generate the invoice
        if (!$payload = $this->invoicegenerator->generate($id)) {
            abort(409, __('lang.error_loading_item'));
        }

        //invoice
        $invoice = $payload['bill'];

        //validate current status
        if ($invoice->bill_status == 'draft') {
            abort(409, __('lang.invoice_still_draft'));
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        $users = $this->userrepo->getClientUsers($invoice->bill_clientid, 'owner', 'collection');
        //other data
        $data = [];
        foreach ($users as $user) {
            $mail = new \App\Mail\PublishInvoice($user, $data, $invoice);
            $mail->build();
        }

        //response
        return new ResendResponse();
    }

    /**
     * Show the form for editing the specified invoice
     * @param object CategoryRepository instance of the repository
     * @param  int  $id invoice id
     * @return \Illuminate\Http\Response
     */
    public function edit(CategoryRepository $categoryrepo, $id) {

        //get the project
        $invoice = $this->invoicerepo->search($id);

        //client categories
        $categories = $categoryrepo->get('invoice');

        //get invoicetags and users tags
        $tags_resource = $this->tagrepo->getByResource('invoice', $id);
        $tags_user = $this->tagrepo->getByType('invoice');
        $tags = $tags_resource->merge($tags_user);
        $tags = $tags->unique('tag_title');
        
        //not found
        if (!$invoice = $invoice->first()) {
            abort(409, __('lang.error_loading_item'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'invoice' => $invoice,
            'categories' => $categories,
            'tags' => $tags,
        ];

        //response
        return new EditResponse($payload);
    }

    /**
     * Update the specified invoicein storage.
     * @param  int  $id invoice id
     * @return \Illuminate\Http\Response
     */
    public function update($id) {
        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'bill_date' => 'required|date',
            'bill_due_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if ($value != '' && request('bill_date') != '' && (strtotime($value) < strtotime(request('bill_date')))) {
                        return $fail(__('lang.due_date_must_be_after_start_date'));
                    }
                }],
            'bill_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
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

        //update
        if (!$this->invoicerepo->update($id)) {
            abort(409);
        }

        //delete & update tags
        $this->tagrepo->delete('invoice', $id);
        $this->tagrepo->add('invoice', $id);

        //get project
        $invoices = $this->invoicerepo->search($id);
        $invoice = $invoices->first();

        //refresh invoice
        $this->invoicerepo->refreshInvoice($invoice);

        //reponse payload
        $payload = [
            'invoices' => $invoices,
            'source' => request('source'),
        ];

        //generate a response
        return new UpdateResponse($payload);

    }

    /**edit
     * Remove the specified invoicefrom storage.
     * @param object DestroyRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function destroy(DestroyRepository $destroyrepo) {

        //delete each record in the array
        $allrows = array();
        foreach (request('ids') as $id => $value) {
            //only checked items
            if ($value == 'on') {
                //delete file
                $destroyrepo->destroyInvoice($id);
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
     * Show the form for updating the invoice
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategory(CategoryRepository $categoryrepo) {

        //get all invoice categories
        $categories = $categoryrepo->get('invoice');

        //reponse payload
        $payload = [
            'categories' => $categories,
        ];

        //show the form
        return new ChangeCategoryResponse($payload);
    }

    /**
     * Show the form for updating the invoice
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function changeCategoryUpdate(CategoryRepository $categoryrepo) {

        //validate the category exists
        if (!\App\Models\Category::Where('category_id', request('category'))
            ->Where('category_type', 'invoice')
            ->first()) {
            abort(409, __('lang.category_not_found'));
        }

        //update each invoice
        $allrows = array();
        foreach (request('ids') as $bill_invoiceid => $value) {
            if ($value == 'on') {
                $invoice = \App\Models\Invoice::Where('bill_invoiceid', $bill_invoiceid)->first();
                //update the category
                $invoice->bill_categoryid = request('category');
                $invoice->save();
                //get the invoice in rendering friendly format
                $invoices = $this->invoicerepo->search($bill_invoiceid);
                //add to array
                $allrows[] = $invoices;
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
     * Show the form for attaching a project to an invoice
     * @return \Illuminate\Http\Response
     */
    public function attachProject() {

        //get client id
        $client_id = request('client_id');

        //reponse payload
        $payload = [
            'projects_feed_url' => url("/feed/projects?ref=clients_projects&client_id=$client_id"),
        ];

        //show the form
        return new AttachProjectResponse($payload);
    }

    /**
     * attach a project to an invoice
     * @return \Illuminate\Http\Response
     */
    public function attachProjectUpdate() {

        //validate the invoice exists
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', request()->route('invoice'))->first();

        //validate the project exists
        if (!$project = \App\Models\Project::Where('project_id', request('attach_project_id'))->first()) {
            abort(409, __('lang.project_not_found'));
        }

        //update the invoice
        $invoice->bill_projectid = request('attach_project_id');
        $invoice->bill_clientid = $project->project_clientid;
        $invoice->save();

        //get refreshed invoice
        $invoices = $this->invoicerepo->search(request()->route('invoice'));
        $invoice = $invoices->first();

        //get all payments and add project
        if ($payments = \App\Models\Payment::Where('payment_invoiceid', request()->route('invoice'))->get()) {
            foreach ($payments as $payment) {
                $payment->payment_projectid = request('attach_project_id');
                $payment->save();
            }
        }

        //refresh invoice
        $this->invoicerepo->refreshInvoice($invoice);

        //reponse payload
        $payload = [
            'invoices' => $invoices,
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * dettach invoice from a project
     * @return \Illuminate\Http\Response
     */
    public function dettachProject() {

        //validate the invoice exists
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', request()->route('invoice'))->first();

        //update the invoice
        $invoice->bill_projectid = null;
        $invoice->save();

        //get refreshed invoice
        $invoices = $this->invoicerepo->search(request()->route('invoice'));

        //get all payments and remove project
        if ($payments = \App\Models\Payment::Where('payment_invoiceid', request()->route('invoice'))->get()) {
            foreach ($payments as $payment) {
                $payment->payment_projectid = null;
                $payment->save();
            }
        }

        //reponse payload
        $payload = [
            'invoices' => $invoices,
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * show the form for cloning an invoice
     * @param object CategoryRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function createClone(CategoryRepository $categoryrepo, $id) {

        //get the invoice
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $id)->first();

        //get tags
        $tags = $this->tagrepo->getByType('invoice');

        //invoice categories
        $categories = $categoryrepo->get('invoice');

        //reponse payload
        $payload = [
            'invoice' => $invoice,
            'tags' => $tags,
            'categories' => $categories,
        ];

        //show the form
        return new CreateCloneResponse($payload);
    }

    /**
     * show the form for cloning an invoice
     * @param object InvoiceClone instance of the request validation
     * @param object CloneInvoiceRepository instance of the repository
     * @return \Illuminate\Http\Response
     */
    public function storeClone(InvoiceClone $request, CloneInvoiceRepository $cloneinvoicerepo, $id) {

        //get the invoice
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $id)->first();

        //clone data
        $data = [
            'invoice_id' => $id,
            'client_id' => request('bill_clientid'),
            'project_id' => request('bill_projectid'),
            'invoice_date' => request('bill_date'),
            'invoice_due_date' => request('bill_due_date'),
            'return' => 'id',
        ];

        //clone invoice
        if (!$invoice_id = $cloneinvoicerepo->clone($data)) {
            abort(409, __('lang.cloning_failed'));
        }

        //reponse payload
        $payload = [
            'id' => $invoice_id,
        ];

        //show the form
        return new StoreCloneResponse($payload);
    }

    /**
     * email a pdf versio to the client
     * @return \Illuminate\Http\Response
     */
    public function emailClient() {

        //validate the invoice exists
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', request('id'))->first();

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => '[TODO]');

        //response
        return response()->json($jsondata);
    }

    /**
     * Show the form for editing the specified invoice
     * @param  int  $invoice invoice id
     * @return \Illuminate\Http\Response
     */
    public function recurringSettings($id) {

        //get the project
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $id)->first();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'invoice' => $invoice,
        ];

        //response
        return new RecurringSettingsResponse($payload);
    }

    /**
     * Update recurring settings
     * @param object InvoiceRecurrringSettings instance of the request validation object
     * @param  int  $invoice invoice id
     * @return \Illuminate\Http\Response
     */
    public function recurringSettingsUpdate(InvoiceRecurrringSettings $request, $id) {

        //get project
        $invoices = $this->invoicerepo->search($id);
        $invoice = $invoices->first();

        //update
        $invoice->bill_recurring = 'yes';
        $invoice->bill_recurring_duration = request('bill_recurring_duration');
        $invoice->bill_recurring_period = request('bill_recurring_period');
        $invoice->bill_recurring_cycles = request('bill_recurring_cycles');
        $invoice->bill_recurring_next = request('bill_recurring_next');

        //refresh invoice
        $this->invoicerepo->refreshInvoice($invoice);

        //reponse payload
        $payload = [
            'page' => $this->pageSettings('edit'),
            'invoices' => $invoices,
        ];

        //response
        return new UpdateResponse($payload);
    }

    /**
     * stop an invoice from recurring
     * @return \Illuminate\Http\Response
     */
    public function stopRecurring() {

        //get the invoice
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', request()->route('invoice'))->first();

        //update the invoice
        $invoice->bill_recurring = 'no';
        $invoice->bill_recurring_duration = null;
        $invoice->bill_recurring_period = null;
        $invoice->bill_recurring_cycles = null;
        $invoice->bill_recurring_next = null;
        $invoice->save();

        //get refreshed invoice
        $invoices = $this->invoicerepo->search(request()->route('invoice'));

        //reponse payload
        $payload = [
            'invoices' => $invoices,
        ];

        //show the form
        return new UpdateResponse($payload);
    }

    /**
     * create line items for ths invoice (from submitted expense items)
     * @param int $bill_invoiceid invoice id
     * @return null
     */
    public function expensesPayload($bill_invoiceid) {

        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $bill_invoiceid)->first();

        //do we have an expense in the payload?
        if (is_array(request('expense_payload'))) {
            foreach (request('expense_payload') as $expense_id) {
                //get the expense
                if ($expense = \App\Models\Expense::Where('expense_id', $expense_id)->first()) {

                    //create a new invoice line item
                    $data['lineitem_description'] = $expense->expense_description;
                    $data['lineitem_rate'] = $expense->expense_amount;
                    $data['lineitem_unit'] = __('lang.item');
                    $data['lineitem_quantity'] = 1;
                    $data['lineitem_total'] = $expense->expense_amount;
                    $data['lineitemresource_linked_type'] = 'expense';
                    $data['lineitemresource_linked_type'] = $expense_id;
                    $data['lineitemresource_type'] = 'invoice';
                    $data['lineitemresource_id'] = $bill_invoiceid;
                    $this->lineitemrepo->create($data);

                    //update expense with invoice id and mark as invoiced
                    $expense->expense_billing_status = 'invoiced';
                    $expense->expense_billable_invoiceid = $bill_invoiceid;
                    $expense->save();
                }
            }
        }
    }

    /**
     * create line items for ths invoice (from submitted expense items)
     * @param object StripePaymentRepository instance of the repository
     * @param object InvoiceRepository instance of the repository
     * @param int $id client id
     */
    public function paymentStripe(StripePaymentRepository $striperepo, InvoiceRepository $invoicerepo, $id) {

        //get invoice
        $invoices = $invoicerepo->search($id);
        $invoice = $invoices->first();

        //payment payload
        $data = [
            'amount' => $invoice->invoice_balance,
            'currency' => config('system.settings_stripe_currency'),
            'invoice_id' => $invoice->bill_invoiceid,
            'cancel_url' => url('invoices/' . $invoice->bill_invoiceid), //in future, this can be bulk payments page
        ];

        //create a new stripe session
        $session_id = $striperepo->onetimePayment($data);

        //reponse payload
        $payload = [
            'session_id' => $session_id,
        ];

        //show the view
        return new StripePaymentResponse($payload);
    }

    /**
     * create line items for ths invoice (from submitted expense items)
     * @param object PaypalPaymentRepository instance of the repository
     * @param object InvoiceRepository instance of the repository
     * @param int $id client id
     */
    public function paymentPaypal(PaypalPaymentRepository $paypalrepo, InvoiceRepository $invoicerepo, $id) {

        //get invoice
        $invoices = $invoicerepo->search($id);
        $invoice = $invoices->first();

        //payment payload
        $data = [
            'amount' => $invoice->invoice_balance,
            'currency' => config('system.settings_paypal_currency'),
            'item_name' => __('lang.invoice_payment'),
            'invoice_id' => $invoice->bill_invoiceid,
            'ipn_url' => url('/api/paypal/ipn'),
            'cancel_url' => url('invoices/' . $invoice->bill_invoiceid), //in future, this can be bulk payments page
        ];

        //create a new paypal session
        $session_id = $paypalrepo->onetimePayment($data);

        //more data
        $data['thank_you_url'] = url('payments/thankyou?session_id=' . $session_id);
        $data['session_id'] = $session_id;

        //reponse payload
        $payload = [
            'paypal' => $data,
        ];

        //show the view
        return new PaypalPaymentResponse($payload);
    }

    /**
     * create line items for ths invoice (from submitted expense items)
     * @param object PaypalPaymentRepository instance of the repository
     * @param object InvoiceRepository instance of the repository
     * @param int $id client id
     */
    public function paymentRazorpay(RazorpayPaymentRepository $razorpayrepo, InvoiceRepository $invoicerepo, $id) {

        //get invoice
        $invoices = $invoicerepo->search($id);
        $invoice = $invoices->first();

        //payment payload
        $payload = [
            'amount' => $invoice->invoice_balance,
            'unit_amount' => $invoice->invoice_balance * 100, //lowest unit (e.g. cents)
            'currency' => config('system.settings_razorpay_currency'),
            'invoice_id' => $invoice->bill_invoiceid,
        ];

        //create a new razorpay session
        if (!$order_id = $razorpayrepo->onetimePayment($payload)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //more data
        $payload['thank_you_url'] = url('payments/thankyou?gateway=razorpay&order_id=' . $order_id);
        $payload['order_id'] = $order_id;
        $payload['company_name'] = config('system.settings_company_name');
        $payload['description'] = __('lang.invoice_payment');
        $payload['image'] = runtimeLogoSmall();
        $payload['thankyou_url'] = url('/payments/thankyou/razorpay');
        $payload['client_name'] = $invoice->client_company_name;
        $payload['client_email'] = auth()->user()->email;
        $payload['key'] = config('system.settings_razorpay_keyid');

        //show the view
        return new RazorpayPaymentResponse($payload);
    }

    /**
     * create line items for ths invoice (from submitted expense items)
     * @param object PaypalPaymentRepository instance of the repository
     * @param object InvoiceRepository instance of the repository
     * @param int $id client id
     */
    public function paymentMollie(MolliePaymentRepository $mollierepo, InvoiceRepository $invoicerepo, $id) {

        //get invoice
        $invoices = $invoicerepo->search($id);
        $invoice = $invoices->first();

        //payment payload
        $payload = [
            'amount' => $invoice->invoice_balance,
            'currency' => config('system.settings_mollie_currency'),
            'invoice_id' => $invoice->bill_invoiceid,
            'thank_you_url' => url('payments/thankyou?gateway=mollie'),
            'webhooks_url' => url('/api/mollie/webhooks'),
        ];

        //create a new razorpay session
        if (!$mollie_url = $mollierepo->onetimePayment($payload)) {
            abort(409, __('lang.error_request_could_not_be_completed'));
        }

        //payload
        $payload = [
            'redirect_url' => $mollie_url,
        ];

        //redirect user
        return new MolliePaymentResponse($payload);
    }

    /**
     * basic settings for the invoices list page
     * @return array
     */
    private function pageSettings($section = '', $data = array()) {

        //common settings
        $page = [
            'crumbs' => [
                __('lang.sales'),
                __('lang.invoices'),
            ],
            'crumbs_special_class' => 'list-pages-crumbs',
            'page' => 'invoices',
            'no_results_message' => __('lang.no_results_found'),
            'mainmenu_invoices' => 'active',
            'mainmenu_sales' => 'active',
            'mainmenu_client_billing' => 'active',
            'submenu_invoices' => 'active',
            'sidepanel_id' => 'sidepanel-filter-invoices',
            'dynamic_search_url' => url('invoices/search?action=search&invoiceresource_id=' . request('invoiceresource_id') . '&invoiceresource_type=' . request('invoiceresource_type')),
            'add_button_classes' => 'add-edit-invoice-button',
            'load_more_button_route' => 'invoices',
            'source' => 'list',
        ];
        //default modal settings (modify for sepecif sections)
        $page += [
            'add_modal_title' => __('lang.add_invoice'),
            'add_modal_create_url' => url('invoices/create?invoiceresource_id=' . request('invoiceresource_id') . '&invoiceresource_type=' . request('invoiceresource_type')),
            'add_modal_action_url' => url('invoices?invoiceresource_id=' . request('invoiceresource_id') . '&invoiceresource_type=' . request('invoiceresource_type')),
            'add_modal_action_ajax_class' => '',
            'add_modal_action_ajax_loading_target' => 'commonModalBody',
            'add_modal_action_method' => 'POST',
        ];

        //invoices list page
        if ($section == 'invoices') {
            $page += [
                'meta_title' => __('lang.invoices'),
                'heading' => __('lang.invoices'),
                'sidepanel_id' => 'sidepanel-filter-invoices',
            ];
            if (request('source') == 'ext') {
                $page += [
                    'list_page_actions_size' => 'col-lg-12',
                ];
            }
            return $page;
        }

        //invoice page
        if ($section == 'invoice') {
            //adjust
            $page['page'] = 'invoice';
            //add
            $page += [
                'crumbs' => [
                    __('lang.invoice'),
                ],
                'crumbs_special_class' => 'main-pages-crumbs',
                'meta_title' => __('lang.invoice') . ' #' . $data->formatted_bill_invoiceid,
                'heading' => __('lang.project') . ' - ' . $data->project_title,
                'bill_invoiceid' => request()->segment(2),
                'source_for_filter_panels' => 'ext',
                'section' => 'overview',
            ];

            //crumbs
            $page['crumbs'] = [
                __('lang.sales'),
                __('lang.invoices'),
                $data['formatted_bill_invoiceid'],
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
            $page['mode'] = 'editing';
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
        $count_all = $this->invoicerepo->search('', ['stats' => 'count-all']);
        $count_due = $this->invoicerepo->search('', ['stats' => 'count-due']);
        $count_overdue = $this->invoicerepo->search('', ['stats' => 'count-overdue']);

        $sum_all = $this->invoicerepo->search('', ['stats' => 'sum-all']);
        $sum_payments = $this->invoicerepo->search('', ['stats' => 'sum-payments']);
        $sum_due_balances = $this->invoicerepo->search('', ['stats' => 'sum-due-balances']);
        $sum_overdue_balances = $this->invoicerepo->search('', ['stats' => 'sum-overdue-balances']);

        //default values
        $stats = [
            [
                'value' => runtimeMoneyFormat($sum_all),
                'title' => __('lang.invoices') . " ($count_all)",
                'percentage' => '100%',
                'color' => 'bg-info',
            ],
            [
                'value' => runtimeMoneyFormat($sum_payments),
                'title' => __('lang.payments'),
                'percentage' => '100%',
                'color' => 'bg-success',
            ],
            [
                'value' => runtimeMoneyFormat($sum_due_balances),
                'title' => __('lang.due') . " ($count_due)",
                'percentage' => '100%',
                'color' => 'bg-warning',
            ],
            [
                'value' => runtimeMoneyFormat($sum_overdue_balances),
                'title' => __('lang.overdue') . " ($count_overdue)",
                'percentage' => '100%',
                'color' => 'bg-danger',
            ],
        ];
        //return
        return $stats;
    }
}