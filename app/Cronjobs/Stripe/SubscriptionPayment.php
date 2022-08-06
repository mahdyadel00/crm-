<?php

/** ---------------------------------------------------------------------------------------------------
 * [Stripe Complete New Subscription Payment]
 * This cronjob checks for recorded webhooks from stripe (checkout.session.completed) and does the following
 *       - Creates a new invoice for the subscription
 *       - Create a new payment for the above invoice
 *       - Marks subscription as active
 *       - Send emails to client and team
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Stripe;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Log;

class SubscriptionPayment {

    public function __invoke(
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        SubscriptionRepository $subscriptionrepo,
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
         *  process the web hoooks. This only processes one subscription at a time (to avoid timeouts)
         */
        $limit = 1;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_gateway_name', 'stripe')
            ->where('webhooks_type', 'checkout.session.completed')
            ->where('webhooks_payment_type', 'subscription')
            ->where('webhooks_status', 'new')->take($limit)->get()) {

            //mark all webhooks in the batch as [processing] - to avoid batch duplicates/collisions
            foreach ($webhooks as $webhook) {
                $webhook->update([
                    'webhooks_status' => 'processing',
                    'webhooks_started_at' => now(),
                ]);
            }

            //log
            Log::info("found applicable webhooks", ['process' => '[cronjob][stripe-subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhooks]);

            //loop and process each webhook in the batch
            foreach ($webhooks as $webhook) {

                Log::info("found valid webhook to process", ['process' => '[cronjob][stripe-subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'webhook' => $webhook]);

                //convert webhook original payload to an object
                $obj = json_decode($webhook->webhooks_payload);

                //check if we have corresponding 'payment_session' for thie webhook
                if (!$session = \App\Models\PaymentSession::Where('session_gateway_ref', $webhook->webhooks_matching_reference)->first()) {
                    //log error
                    Log::critical("no corresponding (payment_session) record was found for this webhook (Checkout Session: $webhook->webhooks_matching_reference)", ['process' => '[cronjob][stripe-subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'session_gateway_ref' => $webhook->webhooks_matching_reference]);
                    $webhook->update([
                        'webhooks_status' => 'failed',
                        'webhooks_comment' => "no corresponding (payment_session) record was found for this webhook. (Checkout Session: $webhook->webhooks_matching_reference)",
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //check if there is a corresponding subscription for the payment session
                if (!$subscription = \App\Models\Subscription::Where('subscription_id', $session->session_subscription)->first()) {
                    //log error
                    Log::critical("no corresponding (subscription) (ID: $session->session_subscription) record was found for this payment session", ['process' => '[cronjob][stripe-subscription-payment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payment_session' => $session]);
                    $webhook->update([
                        'webhooks_status' => 'failed',
                        'webhooks_comment' => "no corresponding invoice (Invoice ID: $session->session_invoices) was found for the payment session (Checkout Session: $session->session_gateway_ref) associated with this webhook",
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //update the subscription with the stripe ID
                $subscription->subscription_gateway_id = $obj->subscription;
                $subscription->subscription_status = 'active';
                $subscription->subscription_date_started = now();
                $subscription->subscription_date_renewed = now();
                $subscription->subscription_date_next_renewal = $subscriptionrepo->nextRenewalDate($subscription);
                $subscription->save();

                //create new invoice
                $invoice = new \App\Models\Invoice();
                $invoice->bill_clientid = $subscription->subscription_clientid;
                $invoice->bill_projectid = $subscription->subscription_projectid;
                $invoice->bill_subscriptionid = $subscription->subscription_id;
                $invoice->bill_creatorid = 0;
                $invoice->bill_date = now();
                $invoice->bill_due_date = now();
                $invoice->bill_subtotal = $subscription->subscription_final_amount;
                $invoice->bill_tax_type = 'none';
                $invoice->bill_final_amount = $subscription->subscription_final_amount;
                $invoice->bill_status = 'paid';
                $invoice->bill_invoice_type = 'subscription';
                $invoice->bill_type = 'invoice';
                $invoice->save();

                //create line item
                $line = new \App\Models\Lineitem();
                $line->lineitem_description = $subscription->subscription_gateway_product_name;
                $line->lineitem_rate = $subscription->subscription_final_amount;
                $line->lineitem_unit = __('lang.each');
                $line->lineitem_quantity = 1;
                $line->lineitem_tax_rate = 0;
                $line->lineitem_tax_amount = 0;
                $line->lineitem_total = $subscription->subscription_final_amount;
                $line->lineitemresource_type = 'invoice';
                $line->lineitemresource_id = $invoice->bill_invoiceid;
                $line->lineitem_position = 1;
                $line->save();

                //create new payment
                $payment = new \App\Models\Payment();
                $payment->payment_creatorid = $session->session_creatorid;
                $payment->payment_date = now();
                $payment->payment_invoiceid = $invoice->bill_invoiceid;
                $payment->payment_clientid = $invoice->bill_clientid;
                $payment->payment_projectid = $invoice->bill_projectid;
                $payment->payment_amount = $session->session_amount;
                $payment->payment_transaction_id = $obj->subscription;
                $payment->payment_subscriptionid = $subscription->subscription_id;
                $payment->payment_gateway = 'Stripe';
                $payment->save();

                //log this event
                $log = new \App\Models\Log();
                $log->log_creatorid = $session->session_creatorid;
                $log->log_text = 'event_paid_the_subscription';
                $log->log_text_type = 'lang';
                $log->log_payload = '';
                $log->logresource_type = 'subscription';
                $log->logresource_id = $subscription->subscription_id;
                $log->save();

                //get refreshed invoice
                $invoices = $invoicerepo->search($invoice->bill_invoiceid);
                $invoice = $invoices->first();

                /** ----------------------------------------------
                 * record event [comment]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => $session->session_creatorid,
                    'event_item' => 'subscription',
                    'event_item_id' => $subscription->subscription_id,
                    'event_item_lang' => 'event_paid_subscription',
                    'event_item_content' => __('lang.subscription') . ' - ' . runtimeSubscriptionIdFormat($subscription->subscription_id),
                    'event_item_content2' => '',
                    'event_parent_type' => 'subscription',
                    'event_parent_id' => $subscription->subscription_id,
                    'event_parent_title' => $subscription->subscription_gateway_product_name,
                    'event_clientid' => $subscription->subscription_clientid,
                    'event_show_item' => 'yes',
                    'event_show_in_timeline' => 'yes',
                    'eventresource_type' => (is_numeric($subscription->subscription_projectid)) ? 'project' : 'subscription',
                    'eventresource_id' => (is_numeric($subscription->subscription_projectid)) ? $subscription->subscription_projectid : $subscription->subscription_id,
                    'event_notification_category' => 'notifications_billing_activity',

                ];
                //record event
                $event_id = $eventrepo->create($data);

                //email data
                $email_data = [
                    'payment_transaction_id' => $obj->subscription,
                    'payment_amount' => runtimeMoneyFormat($subscription->subscription_final_amount),
                    'paid_by_name' => $session->session_creator_fullname,
                    'payment_gateway' => 'Stripe',
                ];

                /** --------------------------------------------------------------------------
                 * send email [team] [queued]
                 * - invoice & payments users, with biling email preference enabled
                 * --------------------------------------------------------------------------*/
                $users = $userrepo->mailingListSubscriptions('email');
                foreach ($users as $user) {
                    //payment received
                    $mail = new \App\Mail\PaymentReceived($user, $email_data, $invoice);
                    $mail->build();
                    //subscription started
                    $mail = new \App\Mail\SubscriptionStarted($user, [], $subscription);
                    $mail->build();
                }
                //track event
                if (is_numeric($event_id)) {
                    $trackingrepo->recordEvent($data, $users, $event_id);
                }

                /** --------------------------------------------------------------------------
                 * send email [client] [queued] (no event tracking for initiator of the event)
                 * - thank you email to user who placed order
                 * - subscription has started
                 * --------------------------------------------------------------------------*/
                if ($user = \App\Models\User::Where('id', $session->session_creatorid)->first()) {
                    //payment thank you
                    $mail = new \App\Mail\PaymentReceived($user, $email_data, $invoice);
                    $mail->build();
                    //subscription started
                    $mail = new \App\Mail\SubscriptionStarted($user, [], $subscription);
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