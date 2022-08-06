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
use Log;

class SubscriptionCancelled extends Mailable {
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
        if (!$template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Subscription Cancelled')->first()) {
            Log::error("creating email failed - template not found", ['process' => '[email-build]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //validate
        if (!$this->obj instanceof \App\Models\Subscription || !$this->user instanceof \App\Models\User) {
            Log::error("creating email failed - invalid payload", ['process' => '[email-build]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
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
            'client_company_name' => $this->obj->client_company_name,
            'subscription_id' => $this->obj->subscription_id,
            'subscription_plan' => $this->obj->subscription_gateway_product_name,
            'subscription_url' => url('/subscriptions/' . $this->obj->subscription_id),
            'subscription_amount' => runtimeMoneyFormat($this->obj->subscription_final_amount),
            'subscription_project_title' => (is_numeric($this->obj->subscription_projectid)) ? $this->obj->project_title : __('lang.none'),
            'subscription_project_id' => (is_numeric($this->obj->subscription_projectid)) ? $this->obj->subscription_projectid : __('lang.none'),
        ];

        //save in the database queue
        $queue = new \App\Models\EmailQueue();
        $queue->emailqueue_to = $this->user->email;
        $queue->emailqueue_subject = $template->parse('subject', $payload);
        $queue->emailqueue_message = $template->parse('body', $payload);
        $queue->save();
    }
}
