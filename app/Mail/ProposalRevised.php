<?php

/** --------------------------------------------------------------------------------
 * [template]
 * This classes renders the [new email] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ProposalRevised extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Proposal Revised')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Proposal) {
            return false;
        }

        //client type user
        if ($this->data['user_type'] == 'client') {
            //validate
            if (!$this->user instanceof \App\Models\User) {
                return false;
            }
            //check if clients emails are disabled
            if ($this->user->type == 'client' && config('system.settings_clients_disable_email_delivery') == 'enabled') {
                return;
            }
            //set vars
            $email = $this->user->email;
            $first_name = $this->user->first_name;
            $last_name = $this->user->first_name;
        }

        
        //lead type user
        if ($this->data['user_type'] == 'lead') {
            //validate
            if (!$this->user instanceof \App\Models\Lead) {
                return false;
            }
            //set vars
            $email = $this->user->lead_email;
            $first_name = $this->user->lead_firstname;
            $last_name = $this->user->lead_lastname;
        }

        //only active templates
        if ($template->emailtemplate_status != 'enabled') {
            return false;
        }

        //get common email variables
        $payload = config('mail.data');

        //set template variables
        $payload += [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'proposal_id' => runtimeProposalIdFormat($this->obj->doc_id),
            'proposal_title' => $this->obj->doc_title,
            'proposal_value' => runtimeMoneyFormat($this->obj->proposal_value),
            'proposal_date' => runtimeDate($this->obj->doc_date_start),
            'proposal_expiry_date' => runtimeDate($this->obj->doc_date_end),
            'proposal_url' => url('/proposals/view/' . $this->obj->doc_unique_id),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->save();
    }
}