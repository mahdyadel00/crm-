<?php

/** --------------------------------------------------------------------------------
 * [template]
 * This classes renders emails that are composed directly in the CRM
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class DirectCRMEmail extends Mailable {
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
    public function __construct($data = []) {

        $this->data = $data;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        //get common email variables
        $payload = config('mail.data');

        //get the temple
        $email = $this->from($this->data['from_email'], $this->data['from_name'])
            ->subject($this->data['email_subject'])
            ->with([
                'content' => $this->data['email_body'],
            ])
            ->view('pages.emails.template');

        //do we have attachements
        if (isset($this->data['attachments']) && is_array($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $directory => $filename) {
                $file_path = BASE_DIR . "/storage/temp/$directory/$filename";
                if (file_exists($file_path)) {
                    $email->attach($file_path);
                }
            }
        }

        //send email
        return $email;
    }
}
