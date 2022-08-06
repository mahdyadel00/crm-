<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Update extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    
     //protected $table = 'projects_assigned';
    protected $primaryKey = 'update_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['update_id'];
    const CREATED_AT = 'update_created';
    const UPDATED_AT = 'update_updated';


}
