<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [project status] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class ProjectStatusChanged extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Project Status Change')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Project || !$this->user instanceof \App\Models\User) {
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
            'project_id' => $this->obj->project_id,
            'project_title' => $this->obj->project_title,
            'project_start_date' => runtimeDate($this->obj->project_date_start),
            'project_due_date' => runtimeDate($this->obj->project_date_due),
            'project_description' => $this->obj->project_description,
            'project_title' => $this->obj->project_title,
            'project_billing_rate' => $this->obj->project_billing_rate,
            'project_id' => $this->obj->project_id,
            'client_name' => $this->obj->client_company_name,
            'client_id' => $this->obj->client_id,
            'project_status' => runtimeSystemLang($this->obj->project_status),
            'project_milestone' => $this->obj->milestone_title,
            'project_category' => $this->obj->category_name,
            'project_url' => url('/projects/' . $this->obj->project_id),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->emailqueue_resourcetype = 'project';
        $queue->emailqueue_resourceid = $this->obj->project_id;
        $queue->save();
    }
}
