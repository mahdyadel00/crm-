<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'expense_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['expense_id'];
    const CREATED_AT = 'expense_created';
    const UPDATED_AT = 'expense_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Expenses
     *         - the Expense belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'expense_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Expense can have many Attachments
     *         - the Attachment belongs to one Expense
     *         - other Attachments can belong to other tables
     */
    public function attachments() {
        return $this->morphMany('App\Models\Attachment', 'attachmentresource');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Expenses
     *         - the Expense belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'expense_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Expense belongs to one Client
     */
    public function client() {
        return $this->belongsTo('App\Models\Client', 'expense_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Expense belongs to one Project
     */
    public function project() {
        return $this->belongsTo('App\Models\Project', 'expense_projectid', 'project_id');
    }

}
