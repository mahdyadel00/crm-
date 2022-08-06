<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['category_id'];
    const CREATED_AT = 'category_created';
    const UPDATED_AT = 'category_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Categories
     *         - the Category belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'category_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Projects
     *         - the Project belongs to one Category
     */
    public function projects() {
        return $this->hasMany('App\Models\Project', 'project_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Projects
     *         - the Project belongs to one Category
     */
    public function leads() {
        return $this->hasMany('App\Models\Lead', 'lead_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Projects
     *         - the Project belongs to one Category
     */
    public function clients() {
        return $this->hasMany('App\Models\Client', 'client_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Projects
     *         - the Project belongs to one Category
     */
    public function invoices() {
        return $this->hasMany('App\Models\Invoice', 'bill_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Estimates
     *         - the Estimate belongs to one Category
     */
    public function estimates() {
        return $this->hasMany('App\Models\Estimate', 'bill_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Contracts
     *         - the Contract belongs to one Category
     */
    public function contracts() {
        return $this->hasMany('App\Models\Contract', 'doc_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Proposal
     *         - the Contract belongs to one Category
     */
    public function proposals() {
        return $this->hasMany('App\Models\Proposal', 'doc_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Expenses
     *         - the Expense belongs to one Category
     */
    public function expenses() {
        return $this->hasMany('App\Models\Expense', 'expense_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Tickets
     *         - the Ticket belongs to one Category
     */
    public function tickets() {
        return $this->hasMany('App\Models\Ticket', 'ticket_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many articles
     *         - the Article belongs to one Category
     */
    public function articles() {
        return $this->hasMany('App\Models\Knowledgebase', 'knowledgebase_categoryid', 'category_id');
    }

    /**
     * The Users that are assigned to the Task.
     */
    public function users() {
        return $this->belongsToMany('App\Models\User', 'category_users', 'categoryuser_categoryid', 'categoryuser_userid');
    }

}
