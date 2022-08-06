<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'ticket_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['ticket_id'];
    const CREATED_AT = 'ticket_created';
    const UPDATED_AT = 'ticket_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Tickets
     *         - the Ticket belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'ticket_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Ticket can have many Comments
     *         - the Comment belongs to one Ticket
     *         - other Comments can belong to other tables
     */
    public function comments() {
        return $this->morphMany('App\Models\Comment', 'commentresource');
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

    /**
     * relatioship business rules:
     *         - the Ticket can have many Tags
     *         - the Tags belongs to one Ticket
     *         - other tags can belong to other tables
     */
    public function tags() {
        return $this->morphMany('App\Models\Tag', 'tagresource');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Ticket
     *         - the Ticket belongs to one Client
     */
    public function client() {
        return $this->belongsTo('App\Models\Client', 'ticket_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Category can have many Tickets
     *         - the Ticket belongs to one Category
     */
    public function category() {
        return $this->belongsTo('App\Models\Category', 'ticket_categoryid', 'category_id');
    }

    /**
     * relatioship business rules:
     *         - the Project can have many Tickets
     *         - the Ticket belongs to one Project
     */
    public function project() {
        return $this->belongsTo('App\Models\Project', 'ticket_categoryid', 'project_id');
    }

    /**
     * relatioship business rules:
     *         - the Ticket can have many Replies
     *         - the Reply belongs to one Ticket
     */
    public function replies() {
        return $this->hasMany('App\Models\TicketReply', 'ticketreply_ticketid', 'ticket_id');
    }

}
