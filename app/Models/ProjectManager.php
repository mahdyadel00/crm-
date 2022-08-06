<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectManager extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'projects_manager';
    protected $primaryKey = 'projectsmanager_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['projectsmanager_id'];
    const CREATED_AT = 'projectsmanager_created';
    const UPDATED_AT = 'projectsmanager_updated';
}
