<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'invoice' => 'App\Models\Invoice',
    'project' => 'App\Models\Project',
    'client' => 'App\Models\Client',
    'lead' => 'App\Models\Lead',
    'task' => 'App\Models\Task',
    'estimate' => 'App\Models\Estimate',
    'ticket' => 'App\Models\Ticket',
    'contract' => 'App\Models\Contract',
    'note' => 'App\Models\Note',
]);

class Tag extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'tag_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['tag_id'];
    const CREATED_AT = 'tag_created';
    const UPDATED_AT = 'tag_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Taags
     *         - the Tag belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'tag_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - clients, project etc can have many Tags
     *   - the Tag can be belong to just one of the above
     *   - Tags table columns named as [tagresource_type tagresource_id]
     */
    public function tagresource() {
        return $this->morphTo();
    }

}
