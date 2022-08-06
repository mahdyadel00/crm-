<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'role_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['role_id'];
    const CREATED_AT = 'role_created';
    const UPDATED_AT = 'role_updated';

    /**
     * get all team level roles
     * @return string
     */
    public function getTeamRoles() {
        return strtolower($this->role->role_name);
    }

    /**
     * relatioship business rules:
     *         - the Project can have many Estimates
     *         - the Estimate belongs to one Project
     */
    public function users() {
        return $this->hasMany('App\Models\User', 'role_id', 'role_id');
    }
}
