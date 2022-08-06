<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProposalTemplate extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'proposal_templates';
    protected $primaryKey = 'proposal_template_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['proposal_template_id'];
    const CREATED_AT = 'proposal_template_created';
    const UPDATED_AT = 'proposal_template_updated';

}
