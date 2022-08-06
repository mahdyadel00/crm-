<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssigned extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'tasks_assigned';
    protected $primaryKey = 'tasksassigned_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['tasksassigned_id'];
    const CREATED_AT = 'tasksassigned_created';
    const UPDATED_AT = 'tasksassigned_updated';
}
