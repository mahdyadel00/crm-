<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [publish revised estimate] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class PublishRevisedEstimate extends Mailable {
    use Queueable;

    /**
     * The data for merging into the email
     */
    public $data;

    /**
     * Model instance
     */
    public $obj;

    /**
     * Model instance
     */
    public $user;

    public $emailerrepo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user = [], $data = [], $obj = []) {

        $this->data = $data;
        $this->user = $user;
        $this->obj = $obj;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        //email template
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Estimate Revised')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Estimate || !$this->user instanceof \App\Models\User) {
            return false;
        }

        //only active templates
        if ($template->emailtemplate_status != 'enabled') {
            return false;
        }

        //check if clients emails are disabled
        if ($this->user->type == 'client' && config('system.settings_clients_disable_email_delivery') == 'enabled') {
            return;
        }

        //get common email variables
        $payload = config('mail.data');

        //set template variables
        $payload += [
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'estimate_id' => runtimeEstimateIdFormat($this->obj->bill_estimateid),
            'estimate_amount' => runtimeMoneyFormat($this->obj->bill_final_amount),
            'estimate_date_created' => runtimeDate($this->obj->bill_date),
            'estimate_expiry_date' => runtimeDate($this->obj->bill_expiry_date),
            'project_title' => $this->obj->project_title,
            'project_id' => $this->obj->project_id,
            'client_name' => $this->obj->client_company_name,
            'client_id' => $this->obj->client_id,
            'estimate_status' => runtimeSystemLang($this->obj->bill_status),
            'estimate_url' => url('/estimates/' . $this->obj->bill_estimateid),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->emailqueue_type = 'pdf';
        $queue->emailqueue_pdf_resource_type = 'estimate';
        $queue->emailqueue_pdf_resource_id = $this->obj->bill_estimateid;
        $queue->emailqueue_resourcetype = 'estimate';
        $queue->emailqueue_resourceid = $this->obj->bill_estimateid;
        $queue->save();
    }
}
