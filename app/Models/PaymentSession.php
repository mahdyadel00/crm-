<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSession extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
         
    protected $table = 'payment_sessions';
    protected $primaryKey = 'template_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['session_id'];
    const CREATED_AT = 'session_created';
    const UPDATED_AT = 'session_updated';


}
