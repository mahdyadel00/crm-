<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Cache;


class User extends Authenticatable {
    use Notifiable;

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['id', 'type'];
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The tasks that are assigned to the user.
     */
    public function role() {
        return $this->hasOne('App\Models\Role', 'role_id', 'role_id');
    }


        /**
     * The tasks that are assigned to the user.
     */
    public function client() {
        return $this->hasOne('App\Models\Client', 'client_id', 'clientid');
    }

    /**
     * relatioship business rules:
     *         - the User can have many Notes
     *         - the Note belongs to one User
     *         - other Note can belong to other tables
     */
    public function notes() {
        return $this->morphMany('App\Models\Note', 'noteresource');
    }

    /**
     * The tasks that are assigned to the user.
     */
    public function assignedTasks() {
        return $this->belongsToMany('App\Models\Task', 'tasks_assigned', 'tasksassigned_userid', 'tasksassigned_taskid');
    }

    /**
     * The tasks that are assigned to the user.
     */
    public function assignedLeads() {
        return $this->belongsToMany('App\Models\Lead', 'leads_assigned', 'leadsassigned_userid', 'leadsassigned_leadid');
    }

    /**
     * The projects that are assigned to the user.
     */
    public function assignedProjects() {
        return $this->belongsToMany('App\Models\Project', 'projects_assigned', 'projectsassigned_userid', 'projectsassigned_projectid');
    }

    /**
     * users notifications
     */
    public function notifications() {
        return $this->hasMany('App\Models\EventTracking', 'eventtracking_userid', 'id');
    }

    /**
     * Always encrypt the password before saving to database
     */
    public function setFPasswordAttribute($value) {
        return bcrypt($value);
    }

    /**
     * count: users projects
     * - always reclaulated fresh. No session data here
     */
    public function getCountProjectsAttribute() {

    }

    /**
     * count: users unread notifications
     * @usage auth()->user()->count_unread_notifications
     */
    public function getCountUnreadNotificationsAttribute() {
        //use notifications relationship (above)
        return $this->notifications->where('eventtracking_status', 'unread')->count();
    }

    /**
     * users full name ucfirst
     */
    public function getFullNameAttribute() {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    /**
     * get the users avatar. if it does not exist return the default avatar
     * @return string
     */
    public function getAvatarAttribute() {
        return getUsersAvatar($this->avatar_directory, $this->avatar_filename);
    }

    /**
     * check if the user has the role of 'administrator'
     * @return bool
     */
    public function getIsAdminAttribute() {
        if (strtolower($this->role->role_id) == 1) {
            return true;
        }
        return false;
    }

    /**
     * check if the user has the type 'client'
     * @return bool
     */
    public function getIsClientAttribute() {
        if (strtolower($this->type) == 'client') {
            return true;
        }
        return false;
    }

    /**
     * check if the user has the type 'client' and also account owner
     * @return bool
     */
    public function getIsClientOwnerAttribute() {
        if (strtolower($this->type) == 'client') {
            if ($this->account_owner == 'yes') {
                return true;
            }
        }
        return false;
    }

    /**
     * check if the user has the type 'team'
     * @return bool
     */
    public function getIsTeamAttribute() {
        if (strtolower($this->type) == 'team') {
            return true;
        }
        return false;
    }

    /**
     * return 'team' or 'contacts'for use in url's like
     * updating user preferences
     * @return bool
     */
    public function getTeamOrContactAttribute() {
        if (strtolower($this->type) == 'team') {
            return 'team';
        }
        return 'contacts';
    }

    /**
     * get the 'name' of this users role
     * @return string
     */
    public function getUserRoleAttribute() {
        return strtolower($this->role->role_name);
    }

    /**
     * format last seen date
     * @return string
     */
    public function getCarbonLastSeenAttribute() {
        if ($this->last_seen == '' || $this->last_seen == null) {
            return '---';
        }
        return \Carbon\Carbon::parse($this->last_seen)->diffForHumans();
    }

    /**
     * is the user online now. Activity is set in the General middle
     *  [usage] if($user->is_online)
     * @return string
     */
    public function getisOnlineAttribute() {
        return Cache::has('user-is-online-' . $this->id);
    }

    /**
     * get the users preferred left menu position. If none is
     * defined, return the default system seting
     * @return string
     */
    public function getLeftMenuPositionAttribute() {

        //none logged in users
        if (!auth()->check()) {
            return config('system.settings_system_default_leftmenu');
        }

        //logged in user
        if ($this->pref_leftmenu_position != '') {
            return $this->pref_leftmenu_position;
        } else {
            return config('system.settings_system_default_leftmenu');
        }
    }

    /**
     * get the users preferred stats panels position. If none is
     * defined, return the default system seting
     * @return string
     */
    public function getStatsPanelPositionAttribute() {

        //none logged in users
        if (!auth()->check()) {
            return config('system.settings_system_default_statspanel');
        }

        //logged in user
        if ($this->pref_statspanel_position != '') {
            return $this->pref_statspanel_position;
        } else {
            return config('system.settings_system_default_statspanel');
        }
    }

    /**
     * check if the user has any permissions to add content, so that we can display the red add button in top nav
     * @return bool
     */
    public function getCanAddContentAttribute() {

        $count = 0;

        //add
        $count += ($this->role->role_clients >= 2) ? 1 : 0;
        $count += ($this->role->role_contacts >= 2) ? 1 : 0;
        $count += ($this->role->role_invoices >= 2) ? 1 : 0;
        $count += ($this->role->role_estimates >= 2) ? 1 : 0;
        $count += ($this->role->role_items >= 2) ? 1 : 0;
        $count += ($this->role->role_tasks >= 2) ? 1 : 0;
        $count += ($this->role->role_projects >= 2) ? 1 : 0;
        $count += ($this->role->role_leads >= 2) ? 1 : 0;
        $count += ($this->role->role_expenses >= 2) ? 1 : 0;
        $count += ($this->role->role_team >= 2) ? 1 : 0;
        $count += ($this->role->role_tickets >= 2) ? 1 : 0;
        $count += ($this->role->role_knowledgebase >= 2) ? 1 : 0;

        return ($count > 0) ? true : false;
    }

    /**
     * Query Scope
     * Left join the client table (used if needed)
     * @param object $query automatically passed by eloquent
     * @return bool
     */
    public function scopeLeftjoinClients($query) {
        $query->leftJoin('clients', function ($leftJoin) {
            $leftJoin->on('clients.client_id', '=', 'users.clientid');
        });
    }

}
