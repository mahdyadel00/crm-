<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

Relation::morphMap([
    'comment' => 'App\Models\Comment',
    'expense' => 'App\Models\Expense',
    'ticket' => 'App\Models\Ticket',
    'ticketreply' => 'App\Models\TicketReply',
]);

class Attachment extends Model {
    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'attachment_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['attachment_id'];
    const CREATED_AT = 'attachment_created';
    const UPDATED_AT = 'attachment_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Attachments
     *         - the Attachment belongs to one User
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'attachment_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *   - Comments, Expense, Tickets etc can have many Attachments
     *   - the Attachment can be belong to just one of the above
     *   - Attachment table columns named as [attachmentresource_type attachmentresource_id]
     */
    public function attachmentresource() {
        return $this->morphTo();
    }

}
