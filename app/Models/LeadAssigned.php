<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadAssigned extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'leads_assigned';
    protected $primaryKey = 'leadsassigned_id';
    protected $guarded = ['leadsassigned_id'];
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'leadsassigned_created';
    const UPDATED_AT = 'leadsassigned_updated';
}
