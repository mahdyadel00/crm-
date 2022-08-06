<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foo extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    //protected $table = 'foobar';
    protected $primaryKey = 'foo_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['foo_id'];
    const CREATED_AT = 'foo_created';
    const UPDATED_AT = 'foo_updated';

}
