<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model {

    /**
     * @primaryKey string - primry key column.
     */
    protected $table = 'email_log';
    protected $primaryKey = 'emaillog_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['emaillog_id'];
    const CREATED_AT = 'emaillog_created';
    const UPDATED_AT = 'emaillog_updated';

}
