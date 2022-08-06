<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [ticket closed] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class TicketClosed extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Ticket Closed')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Ticket || !$this->user instanceof \App\Models\User) {
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
            'by_first_name' => $this->data['by_first_name'],
            'by_last_name' => $this->data['by_last_name'],
            'ticket_id' => $this->obj->ticket_id,
            'ticket_subject' => $this->obj->ticket_subject,
            'ticket_date_created' => runtimeDate($this->obj->ticket_date_created),
            'ticket_message' => $this->obj->ticket_message,
            'project_id' => $this->obj->ticket_projectid,
            'project_title' => $this->obj->project_title,
            'ticket_creator_name' => $this->obj->ticket_title,
            'client_name' => $this->obj->client_company_name,
            'client_id' => $this->obj->client_id,
            'ticket_status' => runtimeSystemLang($this->obj->ticket_status),
            'ticket_priority' => runtimeSystemLang($this->obj->ticket_priority),
            'ticket_category' => $this->obj->category_name,
            'ticket_url' => url('/tickets/' . $this->obj->ticket_id),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->save();
    }
}
