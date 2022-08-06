<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Updating extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    
    protected $table = 'updating';
    protected $primaryKey = 'updating_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['updating_id'];
    const CREATED_AT = 'updating_created';
    const UPDATED_AT = 'updating_updated';


}
