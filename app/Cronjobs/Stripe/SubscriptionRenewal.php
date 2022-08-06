<?php

/** ---------------------------------------------------------------------------------------------------
 * [Stripe Complete Onetime]
 * This cronjob checks for recorded webhooks from stripe (onetime payment) and does the following
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

namespace App\Cronjobs\Stripe;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\StripeRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Log;

class SubscriptionRenewal {

    public function __invoke(
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        SubscriptionRepository $subscriptionrepo,
        StripeRepository $striperepo,
        InvoiceRepository $invoicerepo,
        UserRepository $userrepo
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //log that its run
        Log::info("Cronjob has started", ['process' => '[cronjob][stripe-subscription-renewal]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        /**
         *   - Find webhhoks waiting to be completed
         *   - mark the appropriate invoice as paid
         *   - ecords timeline event & notifications
         *   - Send thank you for your payment email to client
         *   - Send new payment received email to admin
         *   - Limit 20 emails at a time (for performance)
         */
        //Get the emails marked as [pdf] and [invoice] - limit 5
        $limit = 1;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_gateway_name', 'stripe')
            ->where('webhooks_type', 'invoice.payment_succeeded')
            ->where('webhooks_payment_type', 'subscription')
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

                Log::info("stripe cronjob [subscription] - found valid webhook to process", ['process' => '[cronjob][stripe-subscription-renewal]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'webhook' => $webhook]);

                //check if there is a corresponding subscription for the payment session
                if (!$subscription = \App\Models\Subscription::Where('subscription_gateway_id', $webhook->webhooks_matching_reference)->first()) {

                    //log error
                    Log::critical("no corresponding (subscription) (Stripe Subscription ID: $webhook->webhooks_matching_reference) record was found", ['process' => '[stripe-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

                    $webhook->update([
                        'webhooks_status' => 'failed',
                        'webhooks_comment' => "no corresponding (subscription) (Stripe Subscription ID: $webhook->webhooks_matching_reference) record was found",
                    ]);
                    //skip to next webhook in the batch
                    continue;
                }

                //check that his has not already been recorded
                if (\App\Models\Payment::Where('payment_transaction_id', $webhook->webhooks_payment_amount)->exists()) {
                    Log::info("A payment for this event already exists in the database. Will now skip", ['process' => '[stripe-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    //skip to next webhook in the batch
                    continue;
                }

                //get the origianl stripe webook object
                $stripe_obj = json_decode($webhook->webhooks_payload);

                //create new invoice
                $invoice = new \App\Models\Invoice();
                $invoice->bill_clientid = $subscription->subscription_clientid;
                $invoice->bill_projectid = $subscription->subscription_projectid;
                $invoice->bill_subscriptionid = $subscription->subscription_id;
                $invoice->bill_creatorid = 0;
                $invoice->bill_date = now();
                $invoice->bill_due_date = now();
                $invoice->bill_subtotal = $webhook->webhooks_payment_amount;
                $invoice->bill_tax_type = 'none';
                $invoice->bill_final_amount = $webhook->webhooks_payment_amount;
                $invoice->bill_status = 'paid';
                $invoice->bill_invoice_type = 'subscription';
                $invoice->bill_type = 'invoice';
                $invoice->save();

                //create line item
                $line = new \App\Models\Lineitem();
                $line->lineitem_description = $subscription->subscription_gateway_product_name;
                $line->lineitem_rate = $webhook->webhooks_payment_amount;
                $line->lineitem_unit = __('lang.each');
                $line->lineitem_quantity = 1;
                $line->lineitem_tax_rate = 0;
                $line->lineitem_tax_amount = 0;
                $line->lineitem_total = $webhook->webhooks_payment_amount;
                $line->lineitemresource_type = 'invoice';
                $line->lineitemresource_id = $invoice->bill_invoiceid;
                $line->lineitem_position = 1;
                $line->save();

                //create new payment
                $payment = new \App\Models\Payment();
                $payment->payment_creatorid = 0;
                $payment->payment_date = now();
                $payment->payment_invoiceid = $invoice->bill_invoiceid;
                $payment->payment_clientid = $invoice->bill_clientid;
                $payment->payment_projectid = $invoice->bill_projectid;
                $payment->payment_amount = $webhook->webhooks_payment_amount;
                $payment->payment_transaction_id = $webhook->webhooks_payment_transactionid;
                $payment->payment_subscriptionid = $subscription->subscription_id;
                $payment->payment_gateway = 'Stripe';
                $payment->save();

                //get refreshed invoice
                $invoices = $invoicerepo->search($invoice->bill_invoiceid);
                $invoice = $invoices->first();

                //update subscription dates (from the orginal stripe payload)
                $subscription->subscription_date_renewed = \Carbon\Carbon::createFromTimestamp($stripe_obj->created)->format('Y-m-d');
                $subscription->subscription_date_next_renewal = \Carbon\Carbon::createFromTimestamp($stripe_obj->period_end)->addDays(1)->format('Y-m-d');
                $subscription->save();

                //get client primary user
                $client_user = $userrepo->getClientAccountOwner($subscription->subscription_clientid);

                /** ----------------------------------------------
                 * record event [comment]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => $client_user->id,
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
                    'eventresource_type' => 'subscription',
                    'eventresource_id' => $subscription->subscription_id,
                    'event_notification_category' => 'notifications_billing_activity',

                ];

                //get refreshed subscription
                $subscriptions = $subscriptionrepo->search($subscription->subscription_id);
                $subscription = $subscriptions->first();

                /** --------------------------------------------------------------------------
                 * send email [team] [queued]
                 * - invoice & payments users, with biling email preference enabled
                 * --------------------------------------------------------------------------*/
                $users = $userrepo->mailingListSubscriptions('email');
                foreach ($users as $user) {
                    $mail = new \App\Mail\SubscriptionRenewed($user, [], $subscription);
                    $mail->build();
                }

                /** --------------------------------------------------------------------------
                 * send email [client] [queued]
                 * - thank you email to user who placed order
                 * --------------------------------------------------------------------------*/
                $mail = new \App\Mail\SubscriptionRenewed($client_user, [], $subscription);
                $mail->build();

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