<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'project' => 'App\Models\Project',
    'lead' => 'App\Models\Lead',
    'task' => 'App\Models\Task',
]);

class Event extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'event_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['event_id'];
    const CREATED_AT = 'event_created';
    const UPDATED_AT = 'event_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Events
     *         - the Event belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'event_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - projects, leads etc can have many Events
     *   - the Event can be belong to just one of the above
     *   - Note table columns named as [eventresource_type eventresource_id]
     * [note]
     * To get the Project or Lead you need to usereference this very method
     * $note = \App\Models\Note::find(1);
     * $project = $note->noteresource->project;
     *
     */
    public function eventresource() {
        return $this->morphTo();
    }

    /**
     * relatioship business rules:
     *         - the Event can have many Event Trackings
     *         - the Event Tracking belongs to one Event
     */
    public function trackings() {
        return $this->hasMany('App\Models\EventTracking', 'eventtracking_eventid', 'event_id');
    }

}
