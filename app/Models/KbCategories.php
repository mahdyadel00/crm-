<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KbCategories extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'kb_categories';
    protected $primaryKey = 'kbcategory_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['kbcategory_id'];
    const CREATED_AT = 'kbcategory_created';
    const UPDATED_AT = 'kbcategory_updated';

}
