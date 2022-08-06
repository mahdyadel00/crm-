<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'project' => 'App\Models\Project',
    'task' => 'App\Models\Task',
    'ticket' => 'App\Models\Ticket',
]);

class Comment extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'comment_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['comment_id'];
    const CREATED_AT = 'comment_created';
    const UPDATED_AT = 'comment_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Comments
     *         - the Comment belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'comment_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - projects, tasks etc can have many comments
     *   - the assigned can be belong to just one of the above
     *   - comments table columns named as [commentresource_type commentresource_id]
     */
    public function commentresource() {
        return $this->morphTo();
    }

    /**
     * relatioship business rules:
     *         - the Ticket can have many Attachments
     *         - the Attachment belongs to one Ticket
     *         - other Attachments can belong to other tables
     */
    public function attachments() {
        return $this->morphMany('App\Models\Attachment', 'attachmentresource');
    }
}
