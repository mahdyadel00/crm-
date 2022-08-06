<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for payments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Log;

class PaymentRepository {

    /**
     * The payments repository instance.
     */
    protected $payments;

    /**
     * Inject dependecies
     */
    public function __construct(Payment $payments) {
        $this->payments = $payments;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object payment collection
     */
    public function search($id = '', $data = array()) {

        $payments = $this->payments->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        // all client fields
        $payments->selectRaw('*');

        //joins
        $payments->leftJoin('clients', 'clients.client_id', '=', 'payments.payment_clientid');
        $payments->leftJoin('invoices', 'invoices.bill_invoiceid', '=', 'payments.payment_invoiceid');
        $payments->leftJoin('projects', 'projects.project_id', '=', 'payments.payment_projectid');

        //default where
        $payments->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_payment_id')) {
            $payments->where('payment_id', request('filter_payment_id'));
        }
        if (is_numeric($id)) {
            $payments->where('payment_id', $id);
        }

        //filter by client - used for counting on external pages
        if (isset($data['payment_clientid'])) {
            $expenses->where('payment_clientid', $data['payment_clientid']);
        }

        //filter by subscription id
        if (isset($data['payment_subscriptionid'])) {
            $expenses->where('payment_subscriptionid', $data['payment_subscriptionid']);
        }

        //apply filters
        if ($data['apply_filters']) {

            //filter invoice id
            if (request()->filled('filter_payment_invoiceid')) {
                $payments->where('payment_invoiceid', request('filter_payment_invoiceid'));
            }

            //filter: amount (min)
            if (request()->filled('filter_payment_amount_min')) {
                $payments->where('payment_amount', '>=', request('filter_payment_amount_min'));
            }
            //filter: amount (max)
            if (request()->filled('filter_payment_amount_max')) {
                $payments->where('payment_amount', '<=', request('filter_payment_amount_max'));
            }

            //filter: date (start)
            if (request()->filled('filter_payment_date_start')) {
                $payments->whereDate('payment_date', '>=', request('filter_payment_date_start'));
            }

            //filter: date (end)
            if (request()->filled('filter_payment_date_end')) {
                $payments->whereDate('payment_date', '<=', request('filter_payment_date_end'));
            }

            //filter gateway
            if (request()->filled('filter_payment_gateway')) {
                $payments->where('payment_gateway', request('filter_payment_gateway'));
            }

            //filter client id
            if (request()->filled('filter_payment_clientid')) {
                $payments->where('payment_clientid', request('filter_payment_clientid'));
            }

            //filter project id
            if (request()->filled('filter_payment_projectid')) {
                $payments->where('payment_projectid', request('filter_payment_projectid'));
            }

            //resource filtering
            if (request()->filled('paymentresource_type') && request()->filled('paymentresource_id')) {
                switch (request('paymentresource_type')) {
                case 'client':
                    $payments->where('payment_clientid', request('paymentresource_id'));
                    break;
                case 'project':
                    $payments->where('payment_projectid', request('paymentresource_id'));
                    break;
                }
            }

            //stats: - this month
            if (isset($data['stats']) && $data['stats'] == 'sum-today') {
                $payments->where('payment_date', '=', date('Y-m-d'));
            }

            //stats: - this month
            if (isset($data['stats']) && $data['stats'] == 'sum-this-month') {
                $payments->whereMonth('payment_date', '=', date('m'));
                $payments->whereYear('payment_date', '=', date('Y'));
            }

            //stats: - this year
            if (isset($data['stats']) && $data['stats'] == 'sum-this-year') {
                $payments->whereYear('payment_date', '=', date('Y'));
            }

            //search: various client columns and relationships (where first, then wherehas)
            if (request()->filled('search_query') || request()->filled('query')) {
                $payments->where(function ($query) {
                    //clean for invoice id search
                    $bill_invoiceid = str_replace(config('system.settings_invoices_prefix'), '', request('search_query'));
                    $bill_invoiceid = preg_replace("/[^0-9.,]/", '', $bill_invoiceid);
                    $bill_invoiceid = ltrim($bill_invoiceid, '0');
                    $query->Where('payment_invoiceid', '=', $bill_invoiceid);

                    $query->orWhere('payment_gateway', '=', request('search_query'));
                    $query->orWhere('payment_transaction_id', '=', request('search_query'));
                    if (is_numeric(request('search_query'))) {
                        $query->orWhere('payment_amount', '=', request('search_query'));
                    }
                    $query->orWhere('payment_date', '=', date('Y-m-d', strtotime(request('search_query'))));
                    $query->orWhereHas('client', function ($q) {
                        $q->where('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                    });
                    $query->orWhereHas('project', function ($q) {
                        $q->where('project_title', 'LIKE', '%' . request('search_query') . '%');
                    });
                });
            }
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('payments', request('orderby'))) {
                $payments->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $payments->orderBy('client_company_name', request('sortorder'));
                break;
            case 'project':
                $payments->orderBy('project_title', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $payments->orderBy(
                config('settings.ordering_payments.sort_by'),
                config('settings.ordering_payments.sort_order')
            );
        }

        //eager load
        $payments->with([
            'client',
            'invoice',
            'project',
        ]);

        //stats - count all
        if (isset($data['stats']) && $data['stats'] == 'count-all') {
            return $payments->count();
        }
        //stats - sum all
        if (isset($data['stats']) && in_array($data['stats'], [
            'sum-all',
            'sum-today',
            'sum-this-month',
            'sum-this-year',
        ])) {
            return $payments->sum('payment_amount');
        }

        // Get the results and return them.
        if (isset($data['limit']) && is_numeric($data['limit'])) {
            $limit = $data['limit'];
        } else {
            $limit = config('system.settings_system_pagination_limits');
        }

        return $payments->paginate($limit);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $payment = new $this->payments;

        //data
        $payment->payment_date = request('payment_date');
        $payment->payment_invoiceid = request('payment_invoiceid');
        $payment->payment_clientid = request('payment_clientid');
        $payment->payment_projectid = request('payment_projectid');
        $payment->payment_creatorid = request('payment_creatorid');
        $payment->payment_amount = request('payment_amount');
        $payment->payment_transaction_id = request('payment_transaction_id');
        $payment->payment_gateway = request('payment_gateway');
        $payment->payment_notes = request('payment_notes');

        //save and return id
        if ($payment->save()) {
            return $payment->payment_id;
        } else {
            Log::error("record could not be saved - database error", ['process' => '[PaymentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}