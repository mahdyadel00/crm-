<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailQueue extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'email_queue';
    protected $primaryKey = 'emailqueue_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['emailqueue_id'];
    const CREATED_AT = 'emailqueue_created';
    const UPDATED_AT = 'emailqueue_updated';


}
