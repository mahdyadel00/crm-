<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'item_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['item_id'];
    const CREATED_AT = 'item_created';
    const UPDATED_AT = 'item_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Items
     *         - the Item belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'item_creatorid', 'id');
    }

        /**
     * relatioship business rules:
     *         - the Category can have many Invoices
     *         - the Invoice belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'item_categoryid', 'category_id');
    }
    
}
