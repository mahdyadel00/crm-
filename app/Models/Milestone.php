<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Milestone extends Model {
    
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'milestones';
    protected $primaryKey = 'milestone_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['milestone_id'];
    const CREATED_AT = 'milestone_created';
    const UPDATED_AT = 'milestone_updated';


    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Milestone
     *         - the Category belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'milestone_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Project can have many Milestone
     *         - the Milestone belongs to one Project
     */
    public function project() {
        return $this->belongsTo('App\Models\Project', 'milestone_projectid', 'project_id');
    }

    /**
     * relatioship business rules:
     *         - the milestone can have many tasks
     *         - the task belongs to one milestone
     */
    public function tasks() {
        return $this->hasMany('App\Models\Task', 'task_milestoneid', 'milestone_id');
    }

}
