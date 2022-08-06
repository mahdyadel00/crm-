<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Models\Invoice',
    'estimate' => 'App\Models\Estimate',
]);

class Lineitem extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'lineitem_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['lineitem_id'];
    const CREATED_AT = 'lineitem_created';
    const UPDATED_AT = 'lineitem_updated';

    /**
     * relatioship business rules:
     *   - estimates, invoices etc can have many lineitems
     *   - the lineitem can be belong to just one of the above
     *   - lineitems table columns named as [lineitemresource_type lineitemresource_id]
     */
    public function lineitemresource() {
        return $this->morphTo();
    }

    /**
     */
    public function taxes() {
        return $this->morphMany('App\Models\Tax', 'taxresource');
    }

}
