<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomField extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'customfields';
    protected $primaryKey = 'customfields_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['customfields_id'];
    const CREATED_AT = 'customfields_created';
    const UPDATED_AT = 'customfields_updated';

}
