<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadSources extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'leads_sources';
    protected $primaryKey = 'leadsources_id';
    protected $guarded = ['leadsources_id'];
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'leadsources_created';
    const UPDATED_AT = 'leadsources_updated';

}
