<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxrate extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'taxrate_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['taxrate_id'];
    const CREATED_AT = 'taxrate_created';
    const UPDATED_AT = 'taxrate_updated';
}
