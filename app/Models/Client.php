<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'client_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['client_id'];
    const CREATED_AT = 'client_created';
    const UPDATED_AT = 'client_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Clients
     *         - the Client belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'client_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Projects
     *         - the Project belongs to one Client
     */
    public function projects() {
        return $this->hasMany('App\Models\Project', 'project_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Users
     *         - the User belongs to one Client
     */
    public function users() {
        return $this->hasMany('App\Models\User', 'clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Invoices
     *         - the Invoice belongs to one Client
     */
    public function invoices() {
        return $this->hasMany('App\Models\Invoice', 'bill_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Estimates
     *         - the Estimate belongs to one Client
     */
    public function estimates() {
        return $this->hasMany('App\Models\Estimate', 'bill_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Notes
     *         - the Note belongs to one Client
     *         - other Note can belong to other tables (Leads, etc)
     */
    public function notes() {
        return $this->morphMany('App\Models\Note', 'noteresource');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Clients
     *         - the CLient belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'client_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Proposals
     *         - the Proposal belongs to one Client
     */
    public function proposals() {
        return $this->hasMany('App\Models\Proposal', 'doc_lead_id', 'lead_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Contracts
     *         - the Contract belongs to one Client
     */
    public function contracts() {
        return $this->hasMany('App\Models\Contract', 'doc_lead_id', 'lead_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Expenses
     *         - the Expense belongs to one Client
     */
    public function expenses() {
        return $this->hasMany('App\Models\Expense', 'expense_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Files
     *         - the File belongs to one Client
     *         - other Files can belong to other tables (Project, etc)
     */
    public function files() {
        return $this->morphMany('App\Models\File', 'fileresource');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Payments
     *         - the Payment belongs to one Client
     */
    public function payments() {
        return $this->hasMany('App\Models\Payment', 'payment_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Tags
     *         - the Tags belongs to one Client
     *         - other tags can belong to other tables
     */
    public function tags() {
        return $this->morphMany('App\Models\Tag', 'tagresource');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Tickets
     *         - the Ticket belongs to one Client
     */
    public function tickets() {
        return $this->hasMany('App\Models\Ticket', 'ticket_clientid', 'client_id');
    }

    /**
     * Query Scope: Counting a clients projects (of various statuses)
     * @param object $query automatically passed by eloquent
     * @param string $status project status (eg. in_progress)
     * @return bool
     */
    public function scopeCountProjects($query, $status = '') {

        //condition by status
        $conditional = " AND projects.project_status = '$status'";
        $name = "count_projects_$status";

        //all projects
        if ($status == 'all') {
            $conditional = '';
        }

        //pending
        if ($status == 'pending') {
            $conditional = " AND projects.project_status NOT IN('on_hold', 'cancelled', 'completed')";
        }

        //return query
        return $query->selectRaw("(SELECT COUNT(*)
                                          FROM projects
                                          WHERE projects.project_clientid = clients.client_id
                                          $conditional ) AS $name");
    }

    /**
     * Query Scope: Counting a clients invoices (of various statuses)
     * @param object $query automatically passed by eloquent
     * @param string $status invoice status (eg. paid)
     * @return null
     */
    public function scopeCountInvoices($query, $status = '') {

        //condition by status
        $conditional = " AND invoices.bill_status = '$status'";
        $name = "count_invoices_$status";

        //all invoices
        if ($status == 'all') {
            $conditional = '';
        }

        //return query
        $query->selectRaw("(SELECT COUNT(*)
                                   FROM invoices
                                   WHERE invoices.bill_clientid = clients.client_id
                                   $conditional ) AS $name");
    }

    /**
     * Query Scope: Sum of clients invoices (of various statuses)
     * @param object $query automatically passed by eloquent
     * @param string $status invoice status (eg. paid)
     * @return null
     */
    public function scopeSumInvoices($query, $status = '') {

        //condition by status
        $conditional = " AND invoices.bill_status = '$status'";
        $name = "sum_invoices_$status";

        //all invoices
        if ($status == 'all') {
            $conditional = '';
        }

        //query
        return $query->selectRaw("(SELECT SUM(bill_final_amount)
                                          FROM invoices
                                          WHERE invoices.bill_clientid = clients.client_id
                                          $conditional ) AS $name");
    }

    /**
     * Query Scope: Sum of clients payments
     * @param object $query automatically passed by eloquent
     * @param string $status invoice status (eg. paid)
     * @return null
     */
    public function scopeSumPayments($query) {

        //query
        return $query->selectRaw("(SELECT SUM(payment_amount)
                                          FROM payments
                                          WHERE payments.payment_clientid = clients.client_id
                                           ) AS sum_all_payments");
    }

}
