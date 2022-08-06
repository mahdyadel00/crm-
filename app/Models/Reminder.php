<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'project' => 'App\Models\Project',
    'task' => 'App\Models\Task',
    'ticket' => 'App\Models\Ticket',
    'lead' => 'App\Models\Lead',
    'client' => 'App\Models\Client',
    'estimate' => 'App\Models\Estimate',
    'invoice' => 'App\Models\Invoice',
]);

class Reminder extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'reminder_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['reminder_id'];
    const CREATED_AT = 'reminder_created';
    const UPDATED_AT = 'reminder_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many reminders
     *         - the reminder belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'reminder_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - projects, tasks etc can have many reminders
     *   - the assigned can be belong to just one of the above
     *   - reminders table columns named as [reminderresource_type reminderresource_id]
     */
    public function reminderresource() {
        return $this->morphTo();
    }
}
