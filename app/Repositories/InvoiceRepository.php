<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\LineitemRepository;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class InvoiceRepository {

    /**
     * The invoices repository instance.
     */
    protected $invoices;

    /**
     * Inject dependecies
     */
    public function __construct(Invoice $invoices, LineitemRepository $lineitemrepo) {
        $this->invoices = $invoices;
        $this->lineitemrepo = $lineitemrepo;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object invoice collection
     */
    public function search($id = '', $data = array()) {

        $invoices = $this->invoices->newQuery();

        //joins
        $invoices->leftJoin('clients', 'clients.client_id', '=', 'invoices.bill_clientid');
        $invoices->leftJoin('projects', 'projects.project_id', '=', 'invoices.bill_projectid');
        $invoices->leftJoin('categories', 'categories.category_id', '=', 'invoices.bill_categoryid');
        $invoices->leftJoin('users', 'users.id', '=', 'invoices.bill_creatorid');

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $invoices->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'invoices.bill_invoiceid')
                    ->where('reminders.reminderresource_type', '=', 'invoice')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        // all fields
        $invoices->selectRaw('*');

        //count payments
        $invoices->selectRaw('(SELECT COUNT(*)
                                      FROM payments
                                      WHERE payment_invoiceid = invoices.bill_invoiceid)
                                      AS count_payments');

        //sum payments
        $invoices->selectRaw('(SELECT COALESCE(SUM(payment_amount), 0)
                                      FROM payments WHERE payment_invoiceid = invoices.bill_invoiceid
                                      GROUP BY payment_invoiceid)
                                      AS x_sum_payments');
        $invoices->selectRaw('(SELECT COALESCE(x_sum_payments, 0.00))
                                      AS sum_payments');

        //invoice balance
        $invoices->selectRaw('(SELECT COALESCE(bill_final_amount - sum_payments, 0.00))
                                      AS invoice_balance');

        //default where
        $invoices->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_bill_invoiceid')) {
            $invoices->where('bill_invoiceid', request('filter_bill_invoiceid'));
        }
        if (is_numeric($id)) {
            $invoices->where('bill_invoiceid', $id);
        }

        //filter by subscription id
        if (isset($data['bill_subscriptionid'])) {
            $invoices->where('bill_subscriptionid', $data['bill_subscriptionid']);
        }

        //do not show items that not yet ready (i.e exclude items in the process of being cloned that have status 'invisible')
        $invoices->where('bill_visibility', 'visible');

        //filter clients
        if (request()->filled('filter_bill_clientid')) {
            $invoices->where('bill_clientid', request('filter_bill_clientid'));
        }

        //filter projects
        if (request()->filled('filter_bill_projectid')) {
            $invoices->where('bill_projectid', request('filter_bill_projectid'));
        }

        //filter: amount (min)
        if (request()->filled('filter_bill_final_amount_min')) {
            $invoices->where('bill_final_amount', '>=', request('filter_bill_final_amount_min'));
        }

        //filter: amount (max)
        if (request()->filled('filter_bill_final_amount_max')) {
            $invoices->where('bill_final_amount', '<=', request('filter_bill_final_amount_max'));
        }

        //filter: payments (max)
        if (request()->filled('filter_invoice_payments_max')) {
            $invoices->where('sum_payments', '>=', request('filter_invoice_payments_max'));
        }

        //filter: invoice (start)
        if (request()->filled('filter_bill_date_start')) {
            $invoices->where('bill_date', '>=', request('filter_bill_date_start'));
        }

        //filter: invoice (end)
        if (request()->filled('filter_bill_date_end')) {
            $invoices->where('bill_date', '<=', request('filter_bill_date_end'));
        }

        //filter: invoice (start)
        if (request()->filled('filter_bill_due_date_start')) {
            $invoices->whereDate('bill_due_date', '>=', request('filter_bill_due_date_start'));
        }

        //filter: invoice (end)
        if (request()->filled('filter_bill_due_date_end')) {
            $invoices->whereDate('bill_due_date', '<=', request('filter_bill_due_date_end'));
        }

        //resource filtering
        if (request()->filled('invoiceresource_type') && request()->filled('invoiceresource_id')) {
            switch (request('invoiceresource_type')) {
            case 'client':
                $invoices->where('bill_clientid', request('invoiceresource_id'));
                break;
            case 'project':
                $invoices->where('bill_projectid', request('invoiceresource_id'));
                break;
            }
        }

        //filter recurring child invoices
        if (request('filter_recurring_option') == 'recurring_invoices') {
            $invoices->where('bill_recurring', 'yes');
        }
        if (request()->filled('filter_recurring_child') || request('filter_recurring_option') == 'child_invoices') {
            $invoices->where('bill_recurring_child', 'yes');
        }
        if (request()->filled('filter_recurring_parent_id')) {
            $invoices->where('bill_recurring_child', 'yes');
            $invoices->where('bill_recurring_parent_id', request('filter_recurring_parent_id'));
        }

        //stats: - due
        if (isset($data['stats']) && (in_array($data['stats'], [
            'sum-due-balances',
            'count-due',
        ]))) {
            $invoices->where('bill_status', 'due');
        }

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'sum-overdue-balances',
            'count-overdue',
        ]))) {
            $invoices->where('bill_status', 'overdue');
        }

        //stats: - always exclude draft invoices
        if (isset($data['stats']) && (in_array($data['stats'], [
            'count-all',
            'count-due',
            'count-overdue',
            'sum-all',
            'sum-payments',
            'sum-due-balances',
            'sum-overdue-balances',
        ]))) {
            $invoices->whereNotIn('bill_status', ['draft']);
        }

        //filter category
        if (is_array(request('filter_bill_categoryid')) && !empty(array_filter(request('filter_bill_categoryid')))) {
            $invoices->whereIn('bill_categoryid', request('filter_bill_categoryid'));
        }

        //filter created by
        if (is_array(request('filter_bill_creatorid')) && !empty(array_filter(request('filter_bill_creatorid')))) {
            $invoices->whereIn('bill_creatorid', request('filter_bill_creatorid'));
        }

        //filter status
        if (is_array(request('filter_bill_status')) && !empty(array_filter(request('filter_bill_status')))) {
            $invoices->whereIn('bill_status', request('filter_bill_status'));
        }

        //get specified list of invoices
        if (isset($data['list']) && is_array($data['list'])) {
            $invoices->whereIn('bill_invoiceid', $data['list']);
        }

        //filter - exlude draft invoices
        if (request('filter_invoice_exclude_status') == 'draft') {
            $invoices->whereNotIn('bill_status', ['draft']);
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $invoices->where(function ($query) {
                //clean for invoice id search
                $bill_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('search_query'));
                $bill_invoiceid = preg_replace("/[^0-9.,]/", '', $bill_invoiceid);
                $bill_invoiceid = ltrim($bill_invoiceid, '0');
                $query->Where('bill_invoiceid', '=', $bill_invoiceid);

                if (is_numeric(request('search_query'))) {
                    $query->orWhere('bill_final_amount', '=', request('search_query'));
                }
                $query->orWhere('bill_date', '=', date('Y-m-d', strtotime(request('search_query'))));
                $query->orWhere('bill_due_date', '=', date('Y-m-d', strtotime(request('search_query'))));
                $query->orWhere('bill_status', '=', request('search_query'));
                $query->orWhereHas('tags', function ($q) {
                    $q->where('tag_title', 'LIKE', '%' . request('search_query') . '%');
                });
                $query->orWhereHas('category', function ($q) {
                    $q->where('category_name', 'LIKE', '%' . request('search_query') . '%');
                });
                $query->orWhereHas('client', function ($q) {
                    $q->where('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                });
                $query->orWhereHas('project', function ($q) {
                    $q->where('project_title', 'LIKE', '%' . request('search_query') . '%');
                });
            });

        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('invoices', request('orderby'))) {
                $invoices->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $invoices->orderBy('client_company_name', request('sortorder'));
                break;
            case 'project':
                $invoices->orderBy('project_title', request('sortorder'));
                break;
            case 'payments':
                $invoices->orderBy('sum_payments', request('sortorder'));
                break;
            case 'balance':
                $invoices->orderBy('invoice_balance', request('sortorder'));
                break;
            case 'category':
                $invoices->orderBy('category_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $invoices->orderBy(
                config('settings.ordering_invoices.sort_by'),
                config('settings.ordering_invoices.sort_order')
            );
        }

        //eager load
        $invoices->with([
            'payments',
            'tags',
            'taxes',
        ]);

        //stats - sum all
        if (isset($data['stats']) && $data['stats'] == 'sum-all') {
            return $invoices->get()->sum('bill_final_amount');
        }

        //stats - sum balances
        if (isset($data['stats']) && in_array($data['stats'], [
            'sum-payments',
        ])) {
            return $invoices->get()->sum('sum_payments');
        }

        //stats - sum balances
        if (isset($data['stats']) && in_array($data['stats'], [
            'sum-due-balances',
            'sum-overdue-balances',
        ])) {
            return $invoices->get()->sum('invoice_balance');
        }

        //stats - count all
        if (isset($data['stats']) && in_array($data['stats'], [
            'count-all',
            'count-due',
            'count-overdue',
        ])) {
            return $invoices->count();
        }

        // Get the results and return them.
        if (isset($data['limit']) && is_numeric($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = config('system.settings_system_pagination_limits');
        }

        // Get the results and return them.
        return $invoices->paginate($limit);
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $invoice = new $this->invoices;

        //data
        $invoice->bill_clientid = request('bill_clientid');
        $invoice->bill_projectid = request('bill_projectid');
        $invoice->bill_creatorid = auth()->id();
        $invoice->bill_categoryid = request('bill_categoryid');
        $invoice->bill_date = request('bill_date');
        $invoice->bill_due_date = request('bill_due_date');
        $invoice->bill_terms = request('bill_terms');
        $invoice->bill_notes = request('bill_notes');

        //save and return id
        if ($invoice->save()) {
            return $invoice->bill_invoiceid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
        if (!$invoice = $this->invoices->find($id)) {
            return false;
        }

        //general
        $invoice->bill_categoryid = request('bill_categoryid');
        $invoice->bill_date = request('bill_date');
        $invoice->bill_due_date = request('bill_due_date');
        $invoice->bill_notes = request('bill_notes');
        $invoice->bill_terms = request('bill_terms');

        //save
        if ($invoice->save()) {
            return $invoice->bill_invoiceid;
        } else {
            Log::error("unable to update record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * refresh an invoice
     * @param mixed $invoice can be an invoice id or an invoice object
     * @return null
     */
    public function refreshInvoice($invoice) {

        //get the invoice
        if (is_numeric($invoice)) {
            if ($invoices = $this->search($invoice)) {
                $invoice = $invoices->first();
            }
        }

        if (!$invoice instanceof \App\Models\Invoice) {
            return false;
        }

        //change dates to carbon format
        $bill_date = \Carbon\Carbon::parse($invoice->bill_date);
        $bill_due_date = \Carbon\Carbon::parse($invoice->bill_due_date);

        //invoice status for none draft invoices
        if ($invoice->bill_status != 'draft') {

            //invoice status - due
            if ($invoice->invoice_balance > 0) {
                $invoice->bill_status = 'due';
            }

            //invoice status - paid
            if ($invoice->bill_final_amount > 0 && $invoice->invoice_balance <= 0) {
                $invoice->bill_status = 'paid';
            }

            //invoice is overdue
            if ($invoice->bill_status == 'due' || $invoice->bill_status == 'part_paid') {
                if ($bill_due_date->diffInDays(today(), false) > 0) {
                    $invoice->bill_status = 'overdue';
                }
            }

            //overdue invoice with date updated
            if ($invoice->bill_status == 'overdue') {
                if ($bill_due_date->diffInDays(today(), false) < 0) {
                    $invoice->bill_status = 'due';
                }
            }

        }

        //update invoice
        $invoice->save();
    }

    /**
     * update an invoice from he edit invoice page
     * @param int $id record id
     * @return bool
     */
    public function updateInvoice($id) {

        //get the record
        if (!$invoice = $this->invoices->find($id)) {
            return false;
        }

        $invoice->bill_date = request('bill_date');
        $invoice->bill_due_date = request('bill_due_date');
        $invoice->bill_terms = request('bill_terms');
        $invoice->bill_notes = request('bill_notes');
        $invoice->bill_subtotal = request('bill_subtotal');
        $invoice->bill_amount_before_tax = request('bill_amount_before_tax');
        $invoice->bill_final_amount = request('bill_final_amount');
        $invoice->bill_tax_type = request('bill_tax_type');
        $invoice->bill_tax_total_percentage = request('bill_tax_total_percentage');
        $invoice->bill_tax_total_amount = request('bill_tax_total_amount');
        $invoice->bill_discount_type = request('bill_discount_type');
        $invoice->bill_discount_percentage = request('bill_discount_percentage');
        $invoice->bill_discount_amount = request('bill_discount_amount');
        $invoice->bill_adjustment_description = request('bill_adjustment_description');
        $invoice->bill_adjustment_amount = request('bill_adjustment_amount');

        //save
        $invoice->save();
    }

    /**
     * save each invoiceline item
     * (1) get all existing line items and unlink them from expenses or timers
     * (2) delete all existing line items
     * (3) save each line item
     * @param int $bill_invoiceid invoice id
     * @return array
     */
    public function saveLineItems($bill_invoiceid = '') {

        Log::info("saving invoice line items - started", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $bill_invoiceid ?? '']);

        //validation
        if (!is_numeric($bill_invoiceid)) {
            Log::error("validation error - required information is missing", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //unlink linked items (expenses & timers)
        if (!$this->unlinkItems($bill_invoiceid)) {
            return false;
        }

        //delete line items
        \App\Models\Lineitem::Where('lineitemresource_type', 'invoice')
            ->where('lineitemresource_id', $bill_invoiceid)
            ->delete();

        //default position
        $position = 0;

        //loopthrough each posted line item (use description to start the loop)
        if (is_array(request('js_item_description'))) {
            foreach (request('js_item_description') as $key => $description) {

                //next position (simple increment)
                $position++;

                //skip invalid items
                if (request('js_item_description')[$key] == '' || request('js_item_unit')[$key] == '') {
                    Log::error("invalid invoice line item...skipping it", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //skip invalid items
                if (!is_numeric(request('js_item_rate')[$key]) || !is_numeric(request('js_item_total')[$key])) {
                    Log::error("invalid invoice line item...skipping it", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //save lineitem to database
                if (request('js_item_type')[$key] == 'plain') {

                    //validate
                    if (!is_numeric(request('js_item_quantity')[$key])) {
                        Log::error("invalid invoice line item (plain) ...skipping it", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        continue;
                    }

                    $line = [
                        'lineitem_description' => request('js_item_description')[$key],
                        'lineitem_quantity' => request('js_item_quantity')[$key],
                        'lineitem_rate' => request('js_item_rate')[$key],
                        'lineitem_unit' => request('js_item_unit')[$key],
                        'lineitem_total' => request('js_item_total')[$key],
                        'lineitemresource_linked_type' => request('js_item_linked_type')[$key],
                        'lineitemresource_linked_id' => request('js_item_linked_id')[$key],
                        'lineitem_type' => request('js_item_type')[$key],
                        'lineitem_position' => $position,
                        'lineitemresource_type' => 'invoice',
                        'lineitemresource_id' => $bill_invoiceid,
                        'lineitem_time_timers_list' => null,
                        'lineitem_time_hours' => null,
                        'lineitem_time_minutes' => null,
                    ];
                    $this->lineitemrepo->create($line);
                }

                //save time item to database
                if (request('js_item_type')[$key] == 'time') {

                    //validate
                    if (!is_numeric(request('js_item_hours')[$key]) || !is_numeric(request('js_item_minutes')[$key])) {
                        Log::error("invalid invoice line item (time) ...skipping it", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                        continue;
                    }

                    $line = [
                        'lineitem_description' => request('js_item_description')[$key],
                        'lineitem_quantity' => null,
                        'lineitem_rate' => request('js_item_rate')[$key],
                        'lineitem_unit' => request('js_item_unit')[$key],
                        'lineitem_total' => request('js_item_total')[$key],
                        'lineitemresource_linked_type' => request('js_item_linked_type')[$key],
                        'lineitemresource_linked_id' => request('js_item_linked_id')[$key],
                        'lineitem_type' => request('js_item_type')[$key],
                        'lineitem_position' => $position,
                        'lineitemresource_type' => 'invoice',
                        'lineitemresource_id' => $bill_invoiceid,
                        'lineitem_time_hours' => request('js_item_hours')[$key],
                        'lineitem_time_minutes' => request('js_item_minutes')[$key],
                        'lineitem_time_timers_list' => request('js_item_timers_list')[$key],

                    ];
                    $this->lineitemrepo->create($line);
                }

                //[link][expenses]
                if (request('js_item_linked_type')[$key] == 'expense' && request('js_item_linked_id')[$key]) {
                    \App\Models\Expense::where('expense_id', request('js_item_linked_id')[$key])
                        ->update([
                            'expense_billing_status' => 'invoiced',
                            'expense_billable_invoiceid' => $bill_invoiceid,
                        ]);
                }

                //[link][task timers]
                if (request('js_item_linked_type')[$key] == 'timer' && is_numeric(request('js_item_linked_id')[$key])) {
                    $timers = explode(',', request('js_item_timers_list')[$key]);
                    \App\Models\Timer::whereIn('timer_id', $timers)
                        ->update([
                            'timer_billing_status' => 'invoiced',
                            'timer_billing_invoiceid' => $bill_invoiceid,
                        ]);
                }
            }
        }
    }

    /**
     * unlink expenses or tmers linked to a particular invoice
     * @param int $bill_invoiceid invoice id
     * @return bool
     */
    public function unlinkItems($bill_invoiceid = '') {

        if (!is_numeric($bill_invoiceid)) {
            Log::error("validation error - required information is missing", ['process' => '[InvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //[unlink][billed expense]
        \App\Models\Expense::where('expense_billable_invoiceid', $bill_invoiceid)
            ->update([
                'expense_billing_status' => 'not_invoiced',
                'expense_billable_invoiceid' => null,
            ]);

        //[unlink][billed task]
        \App\Models\Timer::where('timer_billing_invoiceid', $bill_invoiceid)
            ->update([
                'timer_billing_status' => 'not_invoiced',
                'timer_billing_invoiceid' => null,
            ]);

        return true;
    }

}