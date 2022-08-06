<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTracking extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'events_tracking';
    protected $primaryKey = 'eventtracking_id';
    protected $guarded = ['eventtracking_id'];
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'eventtracking_created';
    const UPDATED_AT = 'eventtracking_updated';

}