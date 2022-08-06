<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebForm extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */

    protected $table = 'webforms';
    protected $primaryKey = 'webform_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['leadform_id'];
    const CREATED_AT = 'webform_created';
    const UPDATED_AT = 'webform_updated';


    
    /**
     * The Users that are assigned to the Task.
     */
    public function assigned() {
        return $this->belongsToMany('App\Models\User', 'tasks_assigned', 'tasksassigned_taskid', 'tasksassigned_userid');
    }

    

}
