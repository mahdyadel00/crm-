<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'blocks';
    protected $primaryKey = 'block_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['block_id'];
    const CREATED_AT = 'block_created';
    const UPDATED_AT = 'block_updated';

}