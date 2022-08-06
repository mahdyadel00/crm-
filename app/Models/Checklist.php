<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;


Relation::morphMap([
    'lead' => 'App\Models\Lead',
    'task' => 'App\Models\Task',
]);


class Checklist extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'checklist_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['checklist_id'];
    const CREATED_AT = 'checklist_created';
    const UPDATED_AT = 'checklist_updated';

    /**
     * relatioship business rules:
     *   - leads, tasks etc can have many checklists
     *   - the checklist can be belong to just one of the above
     */
    public function checklistresource() {
        return $this->morphTo();
    }

}
