<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebmailTemplate extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'webmail_templates';
    protected $primaryKey = 'webmail_template_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['webmail_template_id'];
    const CREATED_AT = 'webmail_template_created';
    const UPDATED_AT = 'webmail_template_updated';

}
