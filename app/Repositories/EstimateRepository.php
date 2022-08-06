<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for estimates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Estimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class EstimateRepository {

    /**
     * The estimates repository instance.
     */
    protected $estimates;

    /**
     * Inject dependecies
     */
    public function __construct(Estimate $estimates, LineitemRepository $lineitemrepo) {
        $this->estimates = $estimates;
        $this->lineitemrepo = $lineitemrepo;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object estimate collection
     */
    public function search($id = '', $data = []) {

        $estimates = $this->estimates->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //for public url's etc
        if (request('do_not_apply_filters')) {
            $data['apply_filters'] = false;
        }

        // all client fields
        $estimates->selectRaw('*');

        //joins
        $estimates->leftJoin('clients', 'clients.client_id', '=', 'estimates.bill_clientid');
        $estimates->leftJoin('users', 'users.id', '=', 'estimates.bill_creatorid');
        $estimates->leftJoin('categories', 'categories.category_id', '=', 'estimates.bill_categoryid');
        $estimates->leftJoin('projects', 'projects.project_id', '=', 'estimates.bill_projectid');

        //join: users reminders - do not do this for cronjobs
        if (auth()->check()) {
            $estimates->leftJoin('reminders', function ($join) {
                $join->on('reminders.reminderresource_id', '=', 'estimates.bill_estimateid')
                    ->where('reminders.reminderresource_type', '=', 'estimate')
                    ->where('reminders.reminder_userid', '=', auth()->id());
            });
        }

        //default where
        $estimates->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_bill_estimateid')) {
            $estimates->where('bill_estimateid', request('filter_bill_estimateid'));
        }
        if (is_numeric($id)) {
            $estimates->where('bill_estimateid', $id);
        }

        //filter by client - used for counting on external pages
        if (isset($data['bill_projectid'])) {
            $estimates->where('bill_projectid', $data['bill_projectid']);
        }

        //[document templates vs normal estimates]
        if (request('filter_estimate_type') == 'document' || request('estimate_mode') == 'document' || request('generate_estimate_mode') == 'document') {
            $estimates->where('bill_estimate_type', 'document');
        } else {
            $estimates->where('bill_estimate_type', 'estimate');
        }

        //do not show items that not yet ready (i.e exclude items in the process of being cloned that have status 'invisible')
        $estimates->where('bill_visibility', 'visible');

        //apply filters
        if ($data['apply_filters']) {

            //filter clients
            if (request()->filled('filter_bill_clientid')) {
                $estimates->where('bill_clientid', request('filter_bill_clientid'));
            }

            //filter clients
            if (request()->filled('filter_bill_projectid')) {
                $estimates->where('bill_projectid', request('bill_projectid'));
            }

            //filter: value (min)
            if (request()->filled('filter_bill_subtotal_min')) {
                $estimates->where('bill_final_amount', '>=', request('filter_bill_subtotal_min'));
            }

            //filter: value (max)
            if (request()->filled('filter_bill_subtotal_max')) {
                $estimates->where('bill_final_amount', '<=', request('filter_bill_subtotal_max'));
            }

            //filter: estimate date (start)
            if (request()->filled('filter_bill_date_start')) {
                $estimates->where('bill_date', '>=', request('filter_bill_date_start'));
            }

            //filter: estimate date (end)
            if (request()->filled('filter_bill_date_end')) {
                $estimates->where('bill_date', '<=', request('filter_bill_date_end'));
            }

            //filter: estimate date (start)
            if (request()->filled('filter_bill_expiry_date_start')) {
                $estimates->whereDate('bill_expiry_date', '>=', request('filter_bill_expiry_date_start'));
            }

            //filter: estimate date (end)
            if (request()->filled('filter_bill_expiry_date_end')) {
                $estimates->whereDate('bill_expiry_date', '<=', request('filter_bill_expiry_date_end'));
            }

            //resource filtering
            if (request()->filled('estimateresource_type') && request()->filled('estimateresource_id')) {
                switch (request('estimateresource_type')) {
                case 'client':
                    $estimates->where('bill_clientid', request('estimateresource_id'));
                    break;
                case 'project':
                    $estimates->where('bill_projectid', request('estimateresource_id'));
                    break;
                }
            }

            //stats: - count
            if (isset($data['stats']) && (in_array($data['stats'], [
                'count-new',
                'count-accepted',
                'count-declined',
                'count-expired',
            ]))) {
                $estimates->where('bill_status', str_replace('count-', '', $data['stats']));
            }
            //stats: - sum
            if (isset($data['stats']) && (in_array($data['stats'], [
                'sum-new',
                'sum-accepted',
                'sum-declined',
                'sum-expired',
            ]))) {
                $estimates->where('bill_status', str_replace('sum-', '', $data['stats']));
            }

            //filter category
            if (is_array(request('filter_bill_categoryid')) && !empty(array_filter(request('filter_bill_categoryid')))) {
                $estimates->whereIn('bill_categoryid', request('filter_bill_categoryid'));
            }

            //filter status
            if (is_array(request('filter_bill_status')) && !empty(array_filter(request('filter_bill_status')))) {
                $estimates->whereIn('bill_status', request('filter_bill_status'));
            }

            //filter created by
            if (is_array(request('filter_bill_creatorid')) && !empty(array_filter(request('filter_bill_creatorid')))) {
                $estimates->whereIn('bill_creatorid', request('filter_bill_creatorid'));
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $estimates->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

            //filter - exlude draft invoices
            if (request('filter_estimate_exclude_status') == 'draft') {
                $estimates->whereNotIn('bill_status', ['draft']);
            }

            //search: various client columns and relationships (where first, then wherehas)
            if (request()->filled('search_query') || request()->filled('query')) {
                $estimates->where(function ($query) {
                    //clean for estimate id search
                    $bill_estimateid = str_replace(config('system.settings_estimates_prefix'), '', request('search_query'));
                    $bill_estimateid = preg_replace("/[^0-9.,]/", '', $bill_estimateid);
                    $bill_estimateid = ltrim($bill_estimateid, '0');
                    $query->Where('bill_estimateid', '=', $bill_estimateid);

                    $query->orWhere('bill_date', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    $query->orWhere('bill_expiry_date', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                    $query->orWhere('first_name', 'LIKE', '%' . request('search_query') . '%');
                    if (is_numeric(request('search_query'))) {
                        $query->orWhere('bill_final_amount', '=', request('search_query'));
                    }
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
                });
            }
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('estimates', request('orderby'))) {
                $estimates->orderBy(request('orderby'), request('sortorder'));
            }
            //others client
            switch (request('orderby')) {
            case 'client':
                $estimates->orderBy('client_company_name', request('sortorder'));
                break;
            case 'created_by':
                $estimates->orderBy('first_name', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $estimates->orderBy(
                config('settings.ordering_estimates.sort_by'),
                config('settings.ordering_estimates.sort_order')
            );
        }

        //eager load
        $estimates->with([
            'tags',
        ]);

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'sum-new',
            'sum-accepted',
            'sum-declined',
            'sum-expired',
        ]))) {
            return $estimates->get()->sum('bill_final_amount');
        }

        //stats: - overdue
        if (isset($data['stats']) && (in_array($data['stats'], [
            'count-new',
            'count-accepted',
            'count-declined',
            'count-expired',
        ]))) {
            return $estimates->count();
        }

        // Get the results and return them.
        if (isset($data['limit']) && is_numeric($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = config('system.settings_system_pagination_limits');
        }

        return $estimates->paginate($limit);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_clientid = request('bill_clientid');
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_categoryid = request('bill_categoryid');
        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_status = 'draft';
        if (is_numeric(request('bill_projectid'))) {
            $estimate->bill_projectid = request('bill_projectid');
        }

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function createProposalEstimate($id) {

        if (!is_numeric($id)) {
            return;
        }

        //check that we do not already have an estimate
        if (\App\Models\Estimate::Where('bill_proposalid', $id)->Where('bill_estimate_type', 'document')->exists()) {
            return;
        }

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_estimateid = -time();
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_date = now();
        $estimate->bill_status = 'draft';
        $estimate->bill_proposalid = $id;
        $estimate->bill_estimate_type = 'document';

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function createContractEstimate($id) {

        if (!is_numeric($id)) {
            return;
        }

        //check that we do not already have an estimate
        if (\App\Models\Estimate::Where('bill_contractid', $id)->Where('bill_estimate_type', 'document')->exists()) {
            return;
        }

        //save new user
        $estimate = new $this->estimates;

        //data
        $estimate->bill_estimateid = -time();
        $estimate->bill_creatorid = auth()->id();
        $estimate->bill_date = now();
        $estimate->bill_status = 'draft';
        $estimate->bill_contractid = $id;
        $estimate->bill_estimate_type = 'document';

        //save and return id
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to create record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * update a record
     * @param int $id estimate id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$estimate = $this->estimates->find($id)) {
            return false;
        }

        //general
        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_subtotal = request('bill_subtotal');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_categoryid = request('bill_categoryid');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_status = request('bill_status');

        //save
        if ($estimate->save()) {
            return $estimate->bill_estimateid;
        } else {
            Log::error("unable to update record - database error", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate_id' => $id ?? '']);
            return false;
        }
    }

    /**
     * refresh an estimate
     * @param mixed $estimate can be an estimate id or an estimate object
     * @return mixed bool or id of record
     */
    public function refreshEstimate($estimate) {

        //get the estimate
        if (is_numeric($estimate)) {
            if (!$estimate = $this->search($estimate)) {
                return false;
            }
        }

        if (!$estimate instanceof \App\Models\Estimate) {
            Log::error("unable to load estimate record", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //change dates to carbon format
        $bill_date = \Carbon\Carbon::parse($estimate->bill_date);
        $bill_expiry_date = \Carbon\Carbon::parse($estimate->bill_expiry_date);

        //estimate status for none draft, accepted, declined estimates
        if (!in_array($estimate->bill_status, ['draft', 'accepted', 'declined', 'revised'])) {

            //estimate is expired
            if ($estimate->bill_status == 'new') {
                if ($bill_expiry_date->diffInDays(today(), false) > 0) {
                    $estimate->bill_status = 'expired';
                }
            }

            //expired but date updated
            if ($estimate->bill_status == 'expired') {
                if ($bill_expiry_date->diffInDays(today(), false) < 0) {
                    $estimate->bill_status = 'new';
                }
            }

        }

        //update estimate
        $estimate->save();
    }

    /**
     * update an estimate from he edit estimate page
     * @param int $id record id
     * @return null
     */
    public function updateEstimate($id) {

        //get the record
        if (!$estimate = $this->estimates->find($id)) {
            Log::error("unable to load estimate record", ['process' => '[EstimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate_id' => $id ?? '']);
            return false;
        }

        $estimate->bill_date = request('bill_date');
        $estimate->bill_expiry_date = request('bill_expiry_date');
        $estimate->bill_terms = request('bill_terms');
        $estimate->bill_notes = request('bill_notes');
        $estimate->bill_subtotal = request('bill_subtotal');
        $estimate->bill_amount_before_tax = request('bill_amount_before_tax');
        $estimate->bill_final_amount = request('bill_final_amount');
        $estimate->bill_tax_type = request('bill_tax_type');
        $estimate->bill_tax_total_percentage = request('bill_tax_total_percentage');
        $estimate->bill_tax_total_amount = request('bill_tax_total_amount');
        $estimate->bill_discount_type = request('bill_discount_type');
        $estimate->bill_discount_percentage = request('bill_discount_percentage');
        $estimate->bill_discount_amount = request('bill_discount_amount');
        $estimate->bill_adjustment_description = request('bill_adjustment_description');
        $estimate->bill_adjustment_amount = request('bill_adjustment_amount');

        //save
        $estimate->save();
    }

    /**
     * save each estimateline item
     * (1) get all existing line items and unlink them from estimates or timers
     * (2) delete all existing line items
     * (3) save each line item
     * @param int $bill_estimateid resource id
     * @return mixed null|bool
     */
    public function saveLineItems($bill_estimateid = '') {

        Log::info("saving estimate line items - started", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validation
        if (!is_numeric($bill_estimateid)) {
            Log::error("validation error - required information is missing", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete line items
        \App\Models\Lineitem::Where('lineitemresource_type', 'estimate')
            ->where('lineitemresource_id', $bill_estimateid)
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
                    Log::error("invalid estimate line item...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //skip invalid items
                if (!is_numeric(request('js_item_rate')[$key]) || !is_numeric(request('js_item_total')[$key])) {
                    Log::error("invalid estimate line item...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    continue;
                }

                //save lineitem to database
                if (request('js_item_type')[$key] == 'plain') {

                    //validate
                    if (!is_numeric(request('js_item_quantity')[$key])) {
                        Log::error("invalid estimate line item (plain) ...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
                        'lineitemresource_type' => 'estimate',
                        'lineitemresource_id' => $bill_estimateid,
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
                        Log::error("invalid estimate line item (time) ...skipping it", ['process' => '[estimateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
                        'lineitemresource_type' => 'estimate',
                        'lineitemresource_id' => $bill_estimateid,
                        'lineitem_time_hours' => request('js_item_hours')[$key],
                        'lineitem_time_minutes' => request('js_item_minutes')[$key],
                        'lineitem_time_timers_list' => request('js_item_timers_list')[$key],

                    ];
                    $this->lineitemrepo->create($line);
                }
            }
        }
    }

}