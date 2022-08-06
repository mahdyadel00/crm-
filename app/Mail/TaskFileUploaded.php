<?php

/** --------------------------------------------------------------------------------
 * This classes renders the [task file uplaoded] email and stores it in the queue
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class TaskFileUploaded extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Task File Uploaded')->first()) {
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Task || !$this->user instanceof \App\Models\User) {
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
            'file_name' => $this->data['attachment_filename'],
            'file_size' => $this->data['attachment_size'],
            'file_extension' => $this->data['attachment_extension'],
            'file_url' => url('tasks/download-attachment/' . $this->data['attachment_uniqiueid']),
            'uploader_first_name' => $this->data['first_name'],
            'uploader_last_name' => $this->data['last_name'],
            'task_id' => $this->obj->task_id,
            'task_title' => $this->obj->task_title,
            'task_created_date' => runtimeDate($this->obj->task_created),
            'task_date_start' => runtimeDate($this->obj->task_date_start),
            'task_description' => $this->obj->task_description,
            'task_date_due' => runtimeDate($this->obj->task_date_due),
            'project_title' => $this->obj->project_title,
            'project_id' => $this->obj->project_id,
            'client_name' => $this->obj->client_company_name,
            'client_id' => $this->obj->client_id,
            'task_status' => runtimeSystemLang($this->obj->task_status),
            'task_milestone' => $this->obj->milestone_title,
            'task_url' => url('/tasks/v/'.$this->obj->task_id.'/view'),
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
