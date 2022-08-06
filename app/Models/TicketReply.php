<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $table = 'ticket_replies';
    protected $primaryKey = 'ticketreply_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['ticketreply_id'];
    const CREATED_AT = 'ticketreply_created';
    const UPDATED_AT = 'ticketreply_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Replies
     *         - the Reply belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'ticketreply_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Ticket can have many Replies
     *         - the Reply belongs to one Ticket
     */
    public function ticket() {
        return $this->belongsTo('App\Models\Ticket', 'ticketreply_ticketid', 'ticket_id');
    }

    /**
     * relatioship business rules:
     *         - the Ticket reply can have many Attachments
     *         - the Attachment belongs to one Ticket reply
     *         - other Attachments can belong to other tables
     */
    public function attachments() {
        return $this->morphMany('App\Models\Attachment', 'attachmentresource');
    }

}
