<?php

/** --------------------------------------------------------------------------------
 * SendQueued
 * Send emails that are stored in the email queue (database)
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendQueued extends Mailable {
    use Queueable, SerializesModels;

    public $data;

    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $attachment = '') {
        //
        $this->data = $data;
        $this->attachment = $attachment;
    }

    /**
     * Nextloop: This will send the email that has been saved in the database (as sent by the cronjob)
     *
     * @return $this
     */
    public function build() {

        //validate
        if (!$this->data instanceof \App\Models\EmailQueue) {
            return;
        }

        //[attachement] send emil with an attahments
        if (is_array($this->attachment)) {
            return $this->from(config('system.settings_email_from_address'), config('system.settings_email_from_name'))
                ->subject($this->data->emailqueue_subject)
                ->with([
                    'content' => $this->data->emailqueue_message,
                ])
                ->view('pages.emails.template')
                ->attach($this->attachment['filepath'], [
                    'as' => $this->attachment['filename'],
                    'mime' => 'application/pdf',
                ]);
        } else {
            //[no attachment] send email without any attahments
            return $this->from(config('system.settings_email_from_address'), config('system.settings_email_from_name'))
                ->subject($this->data->emailqueue_subject)
                ->with([
                    'content' => $this->data->emailqueue_message,
                ])
                ->view('pages.emails.template');
        }
    }
}
