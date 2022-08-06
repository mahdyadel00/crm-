<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryUser extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'category_users';
    protected $primaryKey = 'categoryuser_id';
    protected $guarded = ['categoryuser_id'];
    protected $dateFormat = 'Y-m-d H:i:s';
    const CREATED_AT = 'categoryuser_created';
    const UPDATED_AT = 'categoryuser_updated';

}