<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model {

    /**
     * @primaryKey string - primry key column.
     * @dateFormat string - date storage format
     * @guarded string - allow mass assignment except specified
     * @CREATED_AT string - creation date column
     * @UPDATED_AT string - updated date column
     */
    protected $primaryKey = 'payment_id';
    protected $dateFormat = 'Y-m-d H:i:s';
    protected $guarded = ['payment_id'];
    const CREATED_AT = 'payment_created';
    const UPDATED_AT = 'payment_updated';

    /**
     * relatioship business rules:
     *         - the Creator (user) can have many Payments
     *         - the Payment belongs to one Creator (user)
     */
    public function creator() {
        return $this->belongsTo('App\Models\User', 'payment_creatorid', 'id');
    }

    /**
     * relatioship business rules:
     *         - the Invoice can have many Payments
     *         - the Payments belongs to one Invoice
     */
    public function invoice() {
        return $this->belongsTo('App\Models\Invoice', 'payment_invoiceid', 'bill_invoiceid');
    }

    /**
     * relatioship business rules:
     *         - the Client can have many Payments
     *         - the Payments belongs to one Client
     */
    public function client() {
        return $this->belongsTo('App\Models\Client', 'payment_clientid', 'client_id');
    }

    /**
     * relatioship business rules:
     *         - the Project can have many Payments
     *         - the Payments belongs to one Project
     */
    public function project() {
        return $this->belongsTo('App\Models\Project', 'payment_projectid', 'project_id');
    }

    /**
     * display format for invoice id - adding leading zeros & with any set prefix
     * e.g. INV-000001
     */
    public function getFormattedInvoiceIdAttribute() {
        return runtimeInvoiceIdFormat($this->payment_invoiceid);
    }

}
