<?php

/** ---------------------------------------------------------------------------------------------------
 * [Complete ALl Onetime]
 * This cronjob checks for recorded webhooks and does the following
 *       - Marks the appropriate invoice as paid
 *       - Records timeline event & notifications
 *       - Send thank you for your payment email to client
 *       - Send new payment received email to admin
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Onetime;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Log;

class OnetimePayment {

    public function __invoke(
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        InvoiceRepository $invoicerepo,
        UserRepository $userrepo

    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        /**
         *   - Find webhhoks waiting to be completed
         *   - mark the appropriate invoice as paid
         *   - ecords timeline event & notifications
         *   - Send thank you for your payment email to client
         *   - Send new payment received email to admin
         *   - Limit 20 emails at a time (for performance)
         */
        //Get the emails marked as [pdf] and [invoice] - limit 5
        $limit = 2;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_payment_type', 'onetime')
            ->where('webhooks_status', 'new')->take($limit)->get()) {

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($webhooks as $webhook) {
                $webhook->update([
                    'webhooks_status' => 'processing',
                    'webhooks_started_at' => now(),
                ]);
            }

            //loop and process each webhook in the batch
            foreach ($webhooks as $webhook) {

                //check if we have corresponding 'payment_session' for thie webhook
                if (!$session = \App\Models\PaymentSession::Where('session_gateway_ref', $webhook->webhooks_matching_reference)->first()) {
                    //log error
                    Log::critical("no corresponding (payment_session) record was found for this webhook (Checkout Session: $webhook->webhooks_matching_reference)", ['process' => '[mollie-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session_gateway_ref' => $webhook->webhooks_matching_reference]);
                    $webhook->update([
                        'webhooks_status' => 'failed',
                        'webhooks_comment' => "no corresponding (payment_session) record was found for this webhook. (Checkout Session: $webhook->webhooks_matching_reference)",
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //check if there is a corresponding invoice for the payment session
                if (!$invoice = \App\Models\Invoice::Where('bill_invoiceid', $session->session_invoices)->first()) {
                    //log error
                    Log::critical("no corresponding (invoice) (Invoice ID: $session->session_invoices) record was found for this payment session", ['process' => '[mollie-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payment_session' => $session]);
                    $webhook->update([
                        'webhooks_status' => 'failed',
                        'webhooks_comment' => "no corresponding invoice (Invoice ID: $session->session_invoices) was found for the payment session (Checkout Session: $session->session_gateway_ref) associated with this webhook",
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //avoid duplicate payments
                if (\App\Models\Payment::Where('payment_transaction_id', $webhook->webhooks_payment_transactionid)->exists()) {
                    Log::info("this webhook payment has already been recorded. transaction id ($webhook->webhooks_payment_transactionid). will skip.", ['process' => '[mollie-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payment_session' => $session]);
                    //mark webhook cronjob as done
                    $webhook->update([
                        'webhooks_status' => 'completed',
                        'webhooks_completed_at' => now(),
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //create new payment
                $payment = new \App\Models\Payment();
                $payment->payment_creatorid = $session->session_creatorid;
                $payment->payment_date = now();
                $payment->payment_invoiceid = $invoice->bill_invoiceid;
                $payment->payment_clientid = $invoice->bill_clientid;
                $payment->payment_projectid = $invoice->bill_projectid;
                $payment->payment_amount = $session->session_amount;
                $payment->payment_transaction_id = $webhook->webhooks_payment_transactionid;
                $payment->payment_gateway = $webhook->webhooks_gateway_name;
                $payment->save();

                //get refreshed invoice
                $invoices = $invoicerepo->search($invoice->bill_invoiceid);
                $invoice = $invoices->first();

                //refresh the invoice
                $invoicerepo->refreshInvoice($invoice);

                /** ----------------------------------------------
                 * record event [comment]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => $session->session_creatorid,
                    'event_item' => 'invoice',
                    'event_item_id' => $invoice->bill_invoiceid,
                    'event_item_lang' => 'event_paid_invoice',
                    'event_item_content' => __('lang.invoice') . ' - ' . $invoice->formatted_bill_invoiceid,
                    'event_item_content2' => '',
                    'event_parent_type' => 'invoice',
                    'event_parent_id' => $invoice->bill_invoiceid,
                    'event_parent_title' => $invoice->project_title,
                    'event_clientid' => $invoice->bill_clientid,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'eventresource_type' => 'project',
                    'eventresource_id' => $invoice->bill_projectid,
                    'event_notification_category' => 'notifications_billing_activity',

                ];
                //record event
                if ($event_id = $eventrepo->create($data)) {
                    //get invoice/payments team users, with billing app notifications enabled
                    $users = $userrepo->mailingListInvoices('app');
                    //record notification
                    $trackingrepo->recordEvent($data, $users, $event_id);
                }

                //additional data for emails
                $data = [
                    'paid_by_name' => $session->session_creator_fullname,
                    'payment_amount' => runtimeMoneyFormat($session->session_amount),
                    'payment_transaction_id' => $webhook->webhooks_payment_transactionid,
                    'payment_gateway' => $session->webhooks_gateway_name,
                ];

                /** --------------------------------------------------------------------------
                 * send email [team] [queued]
                 * - invoice & payments users, with biling email preference enabled
                 * --------------------------------------------------------------------------*/
                $users = $userrepo->mailingListInvoices('email');
                foreach ($users as $user) {
                    $mail = new \App\Mail\PaymentReceived($user, $data, $invoice);
                    $mail->build();
                }

                /** --------------------------------------------------------------------------
                 * send email [client] [queued]
                 * - thank you email to user who placed order
                 * --------------------------------------------------------------------------*/
                if ($user = \App\Models\User::Where('id', $session->session_creatorid)->first()) {
                    $mail = new \App\Mail\PaymentReceived($user, $data, $invoice);
                    $mail->build();
                }

                //mark webhook cronjob as done
                $webhook->update([
                    'webhooks_status' => 'completed',
                    'webhooks_completed_at' => now(),
                ]);

                //reset last cron run data
                \App\Models\Settings::where('settings_id', 1)
                    ->update([
                        'settings_cronjob_has_run' => 'yes',
                        'settings_cronjob_last_run' => now(),
                    ]);
            }
        }

        //[UPCOMING] update database for items marked as processing but never completed. Mark them as 'new'. Based on processing timestamp

    }
}