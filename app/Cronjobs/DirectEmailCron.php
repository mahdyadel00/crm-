<?php

/** ------------------------------------------------------------------------------------------------------------------
 * Email Cron
 * Send emails that were composed in the CRM (e.g. client email)  emails that are stored in the email queue (database)
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-------------------------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Mail\DirectCRMEmail;
use Illuminate\Support\Facades\Mail;
use Log;

class DirectEmailCron {

    public function __invoke() {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        /**
         * Send emails
         *   These emails are being sent every minute. You can set a higher or lower sending limit.
         *   Just note that if there are attachments, a high limit can cause server timeouts
         */
        $limit = 5;
        if ($emails = \App\Models\EmailQueue::Where('emailqueue_type', 'direct')->where('emailqueue_status', 'new')->take($limit)->get()) {

            //log that its run
            Log::info("some emails were found", ['process' => '[cronjob][email-processing]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $emails]);

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($emails as $email) {
                $email->update([
                    'emailqueue_status' => 'processing',
                    'emailqueue_started_at' => now(),
                ]);
            }

            //now process
            foreach ($emails as $email) {

                //send the email (only to a valid email address)
                if ($email->emailqueue_to != '') {

                    /** ----------------------------------------------
                     * send email to client
                     * ----------------------------------------------*/
                    $data = [
                        'email_to' => $email->emailqueue_to,
                        'email_subject' => $email->emailqueue_subject,
                        'email_body' => $email->emailqueue_message,
                        'from_email' => $email->emailqueue_from_email,
                        'from_name' => $email->emailqueue_from_name,
                    ];

                    //add any attachments
                    if ($email->emailqueue_attachments != '') {
                        $attachments = json_decode($email->emailqueue_attachments, true);
                        if (is_array($attachments)) {
                            $data['attachments'] = $attachments;
                        }
                    }
                    Mail::to($email->emailqueue_to)->send(new DirectCRMEmail($data));

                    //log that we sent email
                    $log = new \App\Models\EmailLog();
                    $log->emaillog_email = $email->emailqueue_to;
                    $log->emaillog_subject = $email->emailqueue_subject;
                    $log->emaillog_body = $email->emailqueue_message;
                    $log->save();

                }
                //delete email from the queue
                \App\Models\EmailQueue::Where('emailqueue_id', $email->emailqueue_id)->delete();
            }
        }
    }
}