<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model {
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'contracttemplates_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['contracttemplates_id'];
    const CREATED_AT = 'contracttemplates_created';
    const UPDATED_AT = 'contracttemplates_updated';
}
