<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @proposal    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProposalRepository {

    /**
     * The leads repository instance.
     */
    protected $proposal;

    /**
     * Inject dependecies
     */
    public function __construct(Proposal $proposal) {
        $this->proposal = $proposal;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object proposals collection
     */
    public function search($id = '', $data = []) {

        $proposals = $this->proposal->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        //joins
        $proposals->leftJoin('users', 'users.id', '=', 'proposals.doc_creatorid');
        $proposals->leftJoin('clients', 'clients.client_id', '=', 'proposals.doc_client_id');
        $proposals->leftJoin('leads', 'leads.lead_id', '=', 'proposals.doc_lead_id');
        $proposals->leftJoin('estimates', 'estimates.bill_proposalid', '=', 'proposals.doc_id');

        // all client fields
        $proposals->selectRaw('*');

        //count proposals (all)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals)
                                      AS count_proposals_all");

        //count proposals (draft)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_status = 'draft')
                                      AS count_proposals_draft");

        //count proposals (new)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_status = 'new')
                                      AS count_proposals_new");

        //count proposals (accepted)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_status = 'accepted')
                                      AS count_proposals_accepted");

        //count proposals (declined)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_status = 'declined')
                                      AS count_proposals_declined");

        //count proposals (revised)
        $proposals->selectRaw("(SELECT COUNT(*)
                                      FROM proposals
                                      WHERE doc_status = 'revised')
                                      AS count_proposals_revised");

        //client details - first name
        $proposals->selectRaw("(SELECT first_name
                                      FROM users
                                      WHERE clientid = proposals.doc_client_id AND account_owner = 'yes' LIMIT 1)
                                      AS client_first_name");

        //client details - last name
        $proposals->selectRaw("(SELECT last_name
                                      FROM users
                                      WHERE clientid = proposals.doc_client_id AND account_owner = 'yes' LIMIT 1)
                                      AS client_last_name");

        //sum value: all
        $proposals->selectRaw("(SELECT COALESCE(SUM(bill_final_amount), 0.00)
                                      FROM estimates
                                      WHERE bill_proposalid = proposals.doc_id)
                                      AS proposal_value");

        //default where
        $proposals->whereRaw("1 = 1");

        //filters: id
        if (request()->filled('filter_doc_id')) {
            $proposals->where('doc_id', request('filter_doc_id'));
        }
        if (is_numeric($id)) {
            $proposals->where('doc_id', $id);
        }

        //stats: - count
        if (isset($data['stats']) && (in_array($data['stats'], [
            'count-new',
            'count-accepted',
            'count-declined',
            'count-revised',
            'count-expired',
        ]))) {
            $proposals->where('doc_status', str_replace('count-', '', $data['stats']));
        }

        //stats: - sum
        if (isset($data['stats']) && (in_array($data['stats'], [
            'sum-new',
            'sum-accepted',
            'sum-declined',
            'sum-revised',
            'sum-expired',
        ]))) {
            $proposals->where('doc_status', str_replace('sum-', '', $data['stats']));
        }

        //apply filters
        if ($data['apply_filters']) {

            //filter doc_client_id
            if (request()->filled('filter_doc_client_id')) {
                $proposals->where('doc_client_id', request('filter_doc_client_id'));
            }

            //filter doc_client_id
            if (request()->filled('filter_doc_lead_id')) {
                $proposals->where('doc_lead_id', request('filter_doc_lead_id'));
            }

            //filter proposal id
            if (request()->filled('filter_proposal_id')) {
                $proposals->where('proposal_id', request('filter_proposal_id'));
            }

            //filter: doc_date (start)
            if (request()->filled('filter_doc_date')) {
                $proposals->whereDate('doc_date', '>=', request('filter_doc_date'));
            }

            //filter: doc_date (end)
            if (request()->filled('filter_doc_date')) {
                $proposals->whereDate('doc_date', '<=', request('filter_doc_date'));
            }

            //filter: doc_created (start)
            if (request()->filled('filter_doc_created')) {
                $proposals->whereDate('doc_created', '>=', request('filter_doc_created'));
            }

            //filter: doc_created (end)
            if (request()->filled('filter_doc_created')) {
                $proposals->whereDate('doc_created', '<=', request('filter_doc_created'));
            }

            //filter: doc_date_from (start)
            if (request()->filled('filter_doc_date_start_start')) {
                $proposals->whereDate('doc_date_start', '>=', request('filter_doc_date_start_start'));
            }

            //filter: doc_date_from (end)
            if (request()->filled('filter_doc_date_start_end')) {
                $proposals->whereDate('doc_date_start', '<=', request('filter_doc_date_start_end'));
            }

            //filter: doc_date_end (start)
            if (request()->filled('filter_doc_date_end_start')) {
                $proposals->whereDate('doc_date_end', '>=', request('filter_doc_date_end_start'));
            }

            //filter: doc_date_end (end)
            if (request()->filled('filter_doc_date_end_end')) {
                $proposals->whereDate('doc_date_end', '<=', request('filter_doc_date_end_end'));
            }

            //filter: doc_status
            if (is_array(request('filter_doc_status')) && !empty(array_filter(request('filter_doc_status')))) {
                $proposals->whereIn('doc_status', request('filter_doc_status'));
            }

            //filter: tags
            if (is_array(request('filter_tags')) && !empty(array_filter(request('filter_tags')))) {
                $proposals->whereHas('tags', function ($query) {
                    $query->whereIn('tag_title', request('filter_tags'));
                });
            }

        }

        //filter - exlude draft proposals
        if (request('filter_proposal_exclude_status') == 'draft') {
            $proposals->whereNotIn('doc_status', ['draft']);
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $proposals->where(function ($query) {
                //clean for estimate id search
                $proposal_id = str_replace(config('system.settings_proposals_prefix'), '', request('search_query'));
                $proposal_id = preg_replace("/[^0-9.,]/", '', $proposal_id);
                $proposal_id = ltrim($proposal_id, '0');
                $query->Where('doc_id', '=', $proposal_id);

                $query->orWhere('lead_firstname', '=', request('search_query'));
                $query->orWhere('lead_lastname', '=', request('search_query'));
                $query->orWhere('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_title', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_firstname', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('lead_lastname', 'LIKE', '%' . request('search_query') . '%');
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
            if (Schema::hasColumn('proposals', request('orderby'))) {
                $proposals->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'client':
                $proposals->orderBy('client_company_name', request('sortorder'));
                $proposals->orderBy('lead_firstname', request('sortorder'));
                break;
            case 'value':
                $proposals->orderBy('bill_final_amount', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $proposals->orderBy('doc_id', 'asc');
        }

        //stats - count all
        if (isset($data['stats']) && in_array($data['stats'], [
            'count-new',
            'count-accepted',
            'count-declined',
            'count-revised',
            'count-expired',
        ])) {
            return $proposals->count();
        }

        //stats - sum balances
        if (isset($data['stats']) && in_array($data['stats'], [
            'sum-new',
            'sum-accepted',
            'sum-declined',
            'sum-revised',
            'sum-expired',
        ])) {
            return $proposals->get()->sum('proposal_value');
        }

        // Get the results and return them.
        return $proposals->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * get the company name linked to a proposal
     *
     * @param  doc_id  prposal id
     * @return string
     */
    public function contactDetails($doc_id = '') {

        $details = [
            'company_name' => '---',
            'first_name' => '---',
            'last_name' => '---',
            'email' => '---',
        ];

        if ($proposal = \App\Models\Proposal::Where('doc_id', $doc_id)->first()) {
            //client proposal
            if ($proposal->docresource_type == 'client') {
                //company name
                if ($client = \App\Models\Client::Where('client_id', $proposal->doc_client_id)->first()) {
                    $details['company_name'] = $client->client_company_name;
                }
                //first name
                if ($user = \App\Models\User::Where('clientid', $proposal->doc_client_id)->where('account_owner', 'yes')->first()) {
                    $details['first_name'] = $user->first_name;
                    $details['last_name'] = $user->last_name;
                    $details['email'] = $user->email;
                }
            }
            //lead proposal
            if ($proposal->docresource_type == 'lead') {
                //company name
                if ($lead = \App\Models\Lead::Where('lead_id', $proposal->doc_lead_id)->first()) {
                    //$details['company_name'] = ($lead->lead_company_name != '') ? $lead->lead_company_name : $lead->lead_firstname. ' '. $lead->lead_lastname;
                    $details['company_name'] = $lead->lead_firstname . ' ' . $lead->lead_lastname;
                    $details['first_name'] = $lead->lead_firstname;
                    $details['last_name'] = $lead->lead_lastname;
                    $details['email'] = $lead->lead_email;
                }
            }
        }

        return $details;
    }

}