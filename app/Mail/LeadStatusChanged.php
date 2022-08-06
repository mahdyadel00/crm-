<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [lead status change] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class LeadStatusChanged extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Lead Status Change')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Lead || !$this->user instanceof \App\Models\User) {
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
            'assigned_by_first_name' => auth()->user()->first_name,
            'assigned_by_last_name' => auth()->user()->last_name,
            'lead_id' => $this->obj->lead_id,
            'lead_title' => $this->obj->lead_title,
            'lead_name' => $this->obj->lead_firstname . ' ' . $this->obj->lead_lastname,
            'lead_created_date' => runtimeDate($this->obj->lead_created),
            'lead_value' => $this->obj->lead_value,
            'lead_category' => $this->obj->category_name,
            'lead_description' => $this->obj->lead_description,
            'lead_status' => runtimeSystemLang($this->obj->leadstatus_title),
            'lead_url' => url('/leads'),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->emailqueue_resourcetype = 'lead';
        $queue->emailqueue_resourceid = $this->obj->lead_id;
        $queue->save();
    }
}
