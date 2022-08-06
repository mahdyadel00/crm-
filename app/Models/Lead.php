<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'lead_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['lead_id'];
    const CREATED_AT = 'lead_created';
    const UPDATED_AT = 'lead_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Leads
     *         - the Lead belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'lead_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Projects
     *         - the Project belongs to one User (user)
     */
    public function leadstatus() {
        return $this->belongsTo('App\Models\LeadStatus', 'lead_status', 'leadstatus_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Leads
     *         - the ProLeadject belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'lead_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Tags
     *         - the Tags belongs to one Lead
     *         - other tags can belong to other tables
     */
    public function tags() {
        return $this->morphMany('App\Models\Tag', 'tagresource');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Attachments
     *         - the Attachment belongs to one Lead
     *         - other Attachments can belong to other tables
     */
    public function attachments() {
        return $this->morphMany('App\Models\Attachment', 'attachmentresource');
    }

    /**
     * relatioship business rules:
     *         - the Task can have many Comments
     *         - the Checklist belongs to one Task
     */
    public function checklists() {
        return $this->morphMany('App\Models\Checklist', 'checklistresource');
    }

    /**
     * The assigned users table records
     */
    public function assignedrecords() {
        return $this->hasMany('App\Models\LeadAssigned', 'leadsassigned_leadid', 'lead_id');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Comments
     *         - the Comment belongs to one Lead
     */
    public function comments() {
        return $this->morphMany('App\Models\Comment', 'commentresource');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Events
     *         - the Event belongs to one Lead
     *         - other Event can belong to other tables (Leads, etc)
     */
    public function events() {
        return $this->morphMany('App\Models\Event', 'eventresource');
    }
    /**
     * The Users that are assigned to the Task.
     */
    public function assigned() {
        return $this->belongsToMany('App\Models\User', 'leads_assigned', 'leadsassigned_leadid', 'leadsassigned_userid');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Proposals
     *         - the Proposal belongs to one Lead
     */
    public function proposals() {
        return $this->hasMany('App\Models\Proposal', 'doc_lead_id', 'lead_id');
    }

    /**
     * relatioship business rules:
     *         - the Lead can have many Contracts
     *         - the Contract belongs to one Lead
     */
    public function contracts() {
        return $this->hasMany('App\Models\Contract', 'doc_lead_id', 'lead_id');
    }

    /**
     * leads full name ucfirst
     */
    public function getFullNameAttribute() {
        return ucfirst($this->lead_firstname) . ' ' . ucfirst($this->lead_lastname);
    }

    /**
     * format last contacted date
     * @return string
     */
    public function getCarbonLastContactedAttribute() {
        if ($this->lead_last_contacted == '' || $this->lead_last_contacted == null) {
            return '---';
        }
        return \Carbon\Carbon::parse($this->lead_last_contacted)->diffForHumans();
    }

}
