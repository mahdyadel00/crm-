<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @contract    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ContractRepository {

    /**
     * The leads repository instance.
     */
    protected $contract;

    /**
     * Inject dependecies
     */
    public function __construct(Contract $contract) {
        $this->contract = $contract;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object contracts collection
     */
    public function search($id = '', $data = []) {

        $contracts = $this->contract->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //joins
        $contracts->leftJoin('users', 'users.id', '=', 'contracts.doc_creatorid');
        $contracts->leftJoin('clients', 'clients.client_id', '=', 'contracts.doc_client_id');
        $contracts->leftJoin('projects', 'projects.project_id', '=', 'contracts.doc_project_id');
        $contracts->leftJoin('estimates', 'estimates.bill_contractid', '=', 'contracts.doc_id');

        // all client fields
        $contracts->selectRaw('*');

        //count contracts (all)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts)
                                      AS count_contracts_all");

        //count contracts (draft)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_status = 'draft')
                                      AS count_contracts_draft");

        //count contracts (new)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_status = 'new')
                                      AS count_contracts_new");

        //count contracts (accepted)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_status = 'accepted')
                                      AS count_contracts_accepted");

        //count contracts (declined)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_status = 'declined')
                                      AS count_contracts_declined");

        //count contracts (revised)
        $contracts->selectRaw("(SELECT COUNT(*)
                                      FROM contracts
                                      WHERE doc_status = 'revised')
                                      AS count_contracts_revised");

        //default where
        $contracts->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_doc_id')) {
            $contracts->where('doc_id', request('filter_doc_id'));
        }
        if (is_numeric($id)) {
            $contracts->where('doc_id', $id);
        }

        //apply filters
        if ($data['apply_filters']) {

            //filter doc_client_id
            if (request()->filled('filter_doc_client_id')) {
                $contracts->where('doc_client_id', request('filter_doc_client_id'));
            }

            //filter contract id
            if (request()->filled('filter_contract_id')) {
                $contracts->where('contract_id', request('filter_contract_id'));
            }

            //filter: doc_date (start)
            if (request()->filled('filter_doc_date')) {
                $contracts->whereDate('doc_date', '>=', request('filter_doc_date'));
            }

            //filter: doc_date (end)
            if (request()->filled('filter_doc_date')) {
                $contracts->whereDate('doc_date', '<=', request('filter_doc_date'));
            }

            //filter: doc_created (start)
            if (request()->filled('filter_doc_created')) {
                $contracts->whereDate('doc_created', '>=', request('filter_doc_created'));
            }

            //filter: doc_created (end)
            if (request()->filled('filter_doc_created')) {
                $contracts->whereDate('doc_created', '<=', request('filter_doc_created'));
            }

            //filter: doc_date_from (start)
            if (request()->filled('filter_doc_date_start_start')) {
                $contracts->whereDate('doc_date_start', '>=', request('filter_doc_date_start_start'));
            }

            //filter: doc_date_from (end)
            if (request()->filled('filter_doc_date_start_end')) {
                $contracts->whereDate('doc_date_start', '<=', request('filter_doc_date_start_end'));
            }

            //filter: doc_date_end (start)
            if (request()->filled('filter_doc_date_end_start')) {
                $contracts->whereDate('doc_date_end', '>=', request('filter_doc_date_end_start'));
            }

            //filter: doc_date_end (end)
            if (request()->filled('filter_doc_date_end_end')) {
                $contracts->whereDate('doc_date_end', '<=', request('filter_doc_date_end_end'));
            }

            //filter: doc_status
            if (is_array(request('filter_doc_status')) && !empty(array_filter(request('filter_doc_status')))) {
                $contracts->whereIn('doc_status', request('filter_doc_status'));
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $contracts->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

        }

        //filter - exlude draft invoices
        if (request('filter_exclude_status_draft') == 'draft') {
            $invoices->whereNotIn('doc_status', ['draft']);
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $contracts->where(function ($query) {
                //clean for estimate id search
                $contract_id = str_replace(config('system.settings_contracts_prefix'), '', request('search_query'));
                $contract_id = preg_replace("/[^0-9.,]/", '', $contract_id);
                $contract_id = ltrim($contract_id, '0');
                $query->Where('doc_id', '=', $contract_id);

                $query->orWhere('lead_firstname', '=', request('search_query'));
                $query->orWhere('lead_lastname', '=', request('search_query'));
                $query->orWhere('project_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('doc_date_start', '=', date('Y-m-d', strtotime(request('search_query'))));
                $query->orWhere('doc_date_end', '=', date('Y-m-d', strtotime(request('search_query'))));
                if (is_numeric(request('search_query'))) {
                    $query->orWhere('bill_final_amount', '=', request('search_query'));
                }
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('contracts', request('orderby'))) {
                $contracts->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $contracts->orderBy('client_company_name', request('sortorder'));
                $contracts->orderBy('lead_firstname', request('sortorder'));
                break;
            case 'value':
                $contracts->orderBy('bill_final_amount', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $contracts->orderBy('doc_id', 'asc');
        }

        // Get the results and return them.
        return $contracts->paginate(config('system.settings_system_pagination_limits'));
    }
}