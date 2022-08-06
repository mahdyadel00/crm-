<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'project' => 'App\Models\Project',
    'client' => 'App\Models\Client',
]);

class File extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'file_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['file_id'];
    const CREATED_AT = 'file_created';
    const UPDATED_AT = 'file_updated';

    /**
     * relatioship business rules:
     *   - clients, project etc can have many comments
     *   - the assigned can be belong to just one of the above
     *   - files table columns named as [fileresource_type fileresource_id]
     */
    public function fileresource() {
        return $this->morphTo();
    }

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Files
     *         - the File belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'file_creatorid', 'id');
    }

}
