<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Models\Invoice',
    'lineitem' => 'App\Models\lineitem',
]);

class Tax extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'tax';
    protected $primaryKey = 'tax_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['tax_id'];
    const CREATED_AT = 'tax_created';
    const UPDATED_AT = 'tax_updated';

    /**
     * relatioship business rules:
     *   - projects, tasks etc can have many taxs
     *   - the assigned can be belong to just one of the above
     *   - taxs table columns named as [taxresource_type taxresource_id]
     */
    public function taxresource() {
        return $this->morphTo();
    }
}
