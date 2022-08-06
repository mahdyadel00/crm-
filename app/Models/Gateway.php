<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'gateway_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'gateway_created';
    const UPDATED_AT = 'gateway_updated';


}
