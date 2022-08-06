<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    
     //protected $table = 'projects_assigned';
    protected $primaryKey = 'webhooks_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['template_id'];
    const CREATED_AT = 'webhooks_created';
    const UPDATED_AT = 'webhooks_updated';


}
