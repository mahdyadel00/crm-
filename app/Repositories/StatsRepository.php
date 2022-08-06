<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for stats
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Estimate;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class StatsRepository {

    protected $users;
    protected $invoices;
    protected $payments;
    protected $leads;
    protected $tasks;
    protected $projects;
    protected $expenses;
    protected $estimates;

    /**
     * Inject dependecies
     */
    public function __construct(
        User $users,
        Invoice $invoices,
        Payment $payments,
        Lead $leads,
        Task $tasks,
        Project $projects,
        Expense $expenses,
        Estimate $estimates
    ) {

        $this->users = $users;
        $this->invoices = $invoices;
        $this->payments = $payments;
        $this->tasks = $tasks;
        $this->projects = $projects;
        $this->leads = $leads;
        $this->expenses = $expenses;
        $this->estimates = $estimates;

    }

    /**
     * Sum payments
     *
     * @param array $data for filtering
     *         - type [sum|count] (required)
     *         - date (optional)
     *         - start_date (optional)
     *         - end_date (optional)
     *         - client_id (optional)
     * @return float
     */
    public function sumCountPayments($data = []) {

        $payments = $this->payments->newQuery();

        //default
        $payments->selectRaw('*');

        //default type
        $type = (isset($data['type']) && in_array($data['type'], ['count', 'sum'])) ? $data['type'] : 'count';

        //filter: client
        if (isset($data['client_id']) && is_numeric($data['client_id'])) {
            $payments->where('payment_clientid', $data['client_id']);
        }

        //filter: date
        if (isset($data['date']) && $data['date'] != '') {
            $payments->where('payment_date', $data['date']);
        }

        //filter: date range - start date
        if (isset($data['start_date']) && $data['start_date'] != '') {
            $payments->where('payment_date', '>=', $data['start_date']);
        }

        //date range - end date
        if (isset($data['end_date']) && $data['end_date'] != '') {
            $payments->where('payment_date', '<=', $data['end_date']);
        }

        //sum or count
        if ($type == 'sum') {
            return $payments->sum('payment_amount');
        } else {
            return $payments->count();
        }
    }

    /**
     * Sum expenses
     *
     * @param array $data for filtering
     *         - type [sum|count] (required)
     *         - date (optional)
     *         - start_date (optional)
     *         - end_date (optional)
     *         - client_id (optional)
     * @return float
     */
    public function sumCountExpenses($data = []) {

        $expenses = $this->expenses->newQuery();

        //default
        $expenses->selectRaw('*');

        //default type
        $type = (isset($data['type']) && in_array($data['type'], ['count', 'sum'])) ? $data['type'] : 'count';

        //filter: client
        if (isset($data['client_id']) && is_numeric($data['client_id'])) {
            $expenses->where('expense_clientid', $data['client_id']);
        }

        //filter: date
        if (isset($data['date']) && $data['date'] != '') {
            $expenses->where('expense_date', $data['date']);
        }

        //filter: date range - start date
        if (isset($data['start_date']) && $data['start_date'] != '') {
            $expenses->where('expense_date', '>=', $data['start_date']);
        }

        //date range - end date
        if (isset($data['end_date']) && $data['end_date'] != '') {
            $expenses->where('expense_date', '<=', $data['end_date']);
        }

        //returnsum or count
        if ($type == 'sum') {
            return $expenses->sum('expense_amount');
        } else {
            return $expenses->count();
        }
    }

    /**
     * Sum or count invoices
     * @param array $data for filtering
     *         - type [sum|count] (required)
     *         - date (optional)
     *         - start_date (optional)
     *         - end_date (optional)
     *         - status (optional)
     *         - client_id (optional)
     * @return float
     */
    public function sumCountInvoices($data = []) {

        $invoices = $this->invoices->newQuery();

        //default
        $invoices->selectRaw('*');

        //default type
        $type = (isset($data['type']) && in_array($data['type'], ['count', 'sum'])) ? $data['type'] : 'count';

        //filter: client
        if (isset($data['client_id']) && is_numeric($data['client_id'])) {
            $invoices->where('bill_clientid', $data['client_id']);
        }

        //filter: date
        if (isset($data['date']) && $data['date'] != '') {
            $invoices->where('bill_date', $data['date']);
        }

        //filter:date range - start date
        if (isset($data['start_date']) && $data['start_date'] != '') {
            $invoices->where('bill_date', '>=', $data['start_date']);
        }

        //filter:date range - end date
        if (isset($data['end_date']) && $data['end_date'] != '') {
            $invoices->where('bill_date', '<=', $data['end_date']);
        }

        //filter: status
        if (isset($data['status']) && $data['status'] != '') {
            $invoices->where('bill_status', $data['status']);
        }

        //return sum or count
        if ($type == 'sum') {
            return $invoices->sum('bill_final_amount');
        } else {
            return $invoices->count();
        }
    }

    /**
     * Sum or count estimates
     * @param array $data for filtering
     *         - type [sum|count] (required)
     *         - date (optional)
     *         - start_date (optional)
     *         - end_date (optional)
     *         - status (optional)
     *         - client_id (optional)
     * @return float
     */
    public function sumCountEstimates($data = []) {

        $estimates = $this->estimates->newQuery();

        //default
        $estimates->selectRaw('*');

        //default type
        $type = (isset($data['type']) && in_array($data['type'], ['count', 'sum'])) ? $data['type'] : 'count';

        //filter: client
        if (isset($data['client_id']) && is_numeric($data['client_id'])) {
            $estimates->where('bill_clientid', $data['client_id']);
        }

        //filter: date
        if (isset($data['date']) && $data['date'] != '') {
            $estimates->where('bill_date', $data['date']);
        }

        //filter:date range - start date
        if (isset($data['start_date']) && $data['start_date'] != '') {
            $estimates->where('bill_date', '>=', $data['start_date']);
        }

        //filter:date range - end date
        if (isset($data['end_date']) && $data['end_date'] != '') {
            $estimates->where('bill_date', '<=', $data['end_date']);
        }

        //filter: status
        if (isset($data['status']) && $data['status'] != '') {
            $estimates->where('bill_status', $data['status']);
        }

        //return sum or count
        if ($type == 'sum') {
            return $estimates->sum('bill_final_amount');
        } else {
            return $estimates->count();
        }
    }

    /**
     * Sum invoices
     *
     * @param array $data for filtering
     *         - status  (optional)
     *         - assigned (optional)
     * @return float
     */
    public function countLeads($data = []) {

        $leads = $this->leads->newQuery();

        //default
        $leads->selectRaw('*');

        //status
        if (isset($data['status']) && $data['status'] != '') {
            $leads->where('lead_status', $data['status']);
        }

        //status
        if (isset($data['assigned']) && is_numeric($data['assigned'])) {
            request()->merge(['for_assigned_user' => $data['assigned']]);
            $leads->whereHas('assigned', function ($query) {
                $query->whereIn('leadsassigned_userid', [request('for_assigned_user')]);
            });
        }

        return $leads->count();
    }

    /**
     * count tasks
     * @param array $data for filtering
     *         - status  (optional)
     *         - assigned (optional)
     *         - client_id (optional)
     * @return float
     */
    public function countTasks($data = []) {

        $tasks = $this->tasks->newQuery();

        //default
        $tasks->selectRaw('*');

        //status
        if (isset($data['status']) && $data['status'] != '') {
            if ($data['status'] == 'pending') {
                $tasks->whereNotIn('task_status', ['new', 'in_progress', 'testing', 'awaiting_feedback']);
            } else {
                $tasks->where('task_status', $data['status']);
            }
        }

        //status
        if (isset($data['client_id']) && assigned($data['client_id'])) {
            $tasks->where('task_clientid', $data['client_id']);
        }

        //status
        if (isset($data['assigned']) && is_numeric($data['assigned'])) {
            request()->merge(['for_assigned_user' => $data['assigned']]);
            $tasks->whereHas('assigned', function ($query) {
                $query->whereIn('tasksassigned_userid', [request('for_assigned_user')]);
            });
        }

        return $tasks->count();
    }

    /**
     * count projects
     * @param array $data for filtering
     *         - status  (optional)
     *         - assigned (optional)
     *         - client_id (optional)
     * @return float
     */
    public function countProjects($data = []) {

        $projects = $this->projects->newQuery();

        //default
        $projects->selectRaw('*');

        //status
        if (isset($data['status']) && $data['status'] != '') {
            if ($data['status'] == 'pending') {
                $projects->whereNotIn('project_status', ['completed', 'cancelled']);
            } else {
                $projects->where('project_status', $data['status']);
            }
        }

        //status
        if (isset($data['client_id'])) {
            $projects->where('project_clientid', $data['client_id']);
        }

        //status
        if (isset($data['assigned']) && is_numeric($data['assigned'])) {
            request()->merge(['for_assigned_user' => $data['assigned']]);
            $projects->whereHas('assigned', function ($query) {
                $query->whereIn('projectsassigned_userid', [request('for_assigned_user')]);
            });
        }

        return $projects->count();
    }

    /**
     * Sum all payments for a given period/year. Month by month
     *
     * @param array $data
     *         - period (this_year|last_year|2020)
     *         - client_id (optional)
     *
     * @return array month by month for 12 months ($result[1], $result[2], etc)
     */
    public function sumYearlyIncome($data = []) {

        //defaults
        $results = [
            'total' => 0,
            'year' => '',
            'monthly' => [],
        ];

        //payments for a whole year
        if (isset($data['period']) && (in_array($data['period'], ['this_year', 'last_year']) || is_numeric($data['period']))) {
            //get yearly payments
            switch ($data['period']) {
            case 'this_year':
                $year = \Carbon\Carbon::now()->format('Y');
                break;
            case 'last_year':
                $year = \Carbon\Carbon::now()->subYear()->format('Y');
                break;
            case (is_numeric($data['period'])):
                $year = $data['period'];
                break;
            }
            //every month of the year
            for ($i = 1; $i <= 12; $i++) {
                //get first and last days of the month
                $month = $i;
                //amount
                $amount = $this->sumCountPayments(
                    [
                        'type' => 'sum',
                        'start_date' => \Carbon\Carbon::create($year, $month)->startOfMonth()->format('Y-m-d'),
                        'end_date' => \Carbon\Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d'),
                        'client_id' => (isset($data['client_id'])) ? $data['client_id'] : '',
                    ]);
                //get income for the month
                $results['monthly'][] = $amount;
                //running total
                $results['total'] += $amount;
            }
            //add the year to the array
            $results['year'] = $year;
        }

        //results
        return $results;
    }

    /**
     * Sum all payments for a given period/year. Month by month
     *
     * @param array $data
     *         - period (this_year|last_year|2020)
     *         - client_id (optional)
     *
     * @return array month by month for 12 months ($result[1], $result[2], etc)
     */
    public function sumYearlyExpenses($data = []) {

        //defaults
        $results = [
            'total' => 0,
            'year' => '',
            'monthly' => [],
        ];

        //payments for a whole year
        if (isset($data['period']) && (in_array($data['period'], ['this_year', 'last_year']) || is_numeric($data['period']))) {
            //get yearly payments
            switch ($data['period']) {
            case 'this_year':
                $year = \Carbon\Carbon::now()->format('Y');
                break;
            case 'last_year':
                $year = \Carbon\Carbon::now()->subYear()->format('Y');
                break;
            case (is_numeric($data['period'])):
                $year = $data['period'];
                break;
            }
            //every month of the year
            for ($i = 1; $i <= 12; $i++) {
                //get first and last days of the month
                $month = $i;
                //amount
                $amount = $this->sumCountExpenses(
                    [
                        'type' => 'sum',
                        'start_date' => \Carbon\Carbon::create($year, $month)->startOfMonth()->format('Y-m-d'),
                        'end_date' => \Carbon\Carbon::create($year, $month)->lastOfMonth()->format('Y-m-d'),
                        'client_id' => (isset($data['client_id'])) ? $data['client_id'] : '',
                    ]);
                //get income for the month
                $results['monthly'][] = $amount;
                //running total
                $results['total'] += $amount;
            }
            //add the year to the array
            $results['year'] = $year;
        }

        //results
        return $results;
    }
}