<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'project' => 'App\Models\Project',
    'client' => 'App\Models\Client',
    'user' => 'App\Models\User',
    'lead' => 'App\Models\Lead',
]);

class Note extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'note_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['note_id'];
    const CREATED_AT = 'note_created';
    const UPDATED_AT = 'note_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Notes
     *         - the Note belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'note_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - projects, leads etc can have many Notes
     *   - the Note can be belong to just one of the above
     *   - Note table columns named as [noteresource_type noteresource_id]
     * [note]
     * To get the Project or Task you need to usereference this very method
     * $note = \App\Models\Note::find(1);
     * $project = $note->noteresource->project;
     *
     */
    public function noteresource() {
        return $this->morphTo();
    }

    /**
     * relatioship business rules:
     *         - the Project can have many Tags
     *         - the Tags belongs to one Project
     *         - other tags can belong to other tables
     */
    public function tags() {
        return $this->morphMany('App\Models\Tag', 'tagresource');
    }

}
