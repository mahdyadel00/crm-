<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'leads_status';
    protected $primaryKey = 'leadstatus_id';
    protected $guarded = ['leadstatus_id'];
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'leadstatus_created';
    const UPDATED_AT = 'leadstatus_updated';

    /**
     * relatioship business rules:
     *         - the Lead Status can have many Leads
     *         - the Lead belongs to one Lead Status
     */
    public function leads() {
        return $this->hasMany('App\Models\Lead', 'lead_status', 'leadstatus_id');
    }

}
