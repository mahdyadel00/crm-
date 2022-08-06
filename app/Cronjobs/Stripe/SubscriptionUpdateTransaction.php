<?php

/** ---------------------------------------------------------------------------------------------------
 * [Stripe Complete Onetime]
 * This cronjob checks update initial subscription payments with the actual stripe transaction ID
 * Reason is that when a new subscription is first paid, we use the 'checkout.session.completed'
 * which does not contain a stripe transaction ID. We get the transaction from the next webhook
 * (invoice.payment_succeeded) which what this is processing
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 *      - the scheduler is set to run this every minuted
 *      - the schedler itself is evoked by the signle cronjob set in cpanel (which runs every minute)
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Stripe;
use Log;

class SubscriptionUpdateTransaction {

    public function __invoke(
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        /**
         *   - Update the transaction ID of the very first payment done \
         *     for a subscription. Thsi payment would have been given a temp
         *     transaction ID using the subscription ID         */
        $limit = 5;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_gateway_name', 'stripe')
            ->where('webhooks_type', 'crm-subscription-transation-id')->take($limit)->get()) {

            //log that its run
            Log::info("found applicable webhooks", ['process' => '[cronjob][stripe-subscription-uodate-trasnaction-id]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhooks]);

            //mark all emails in the batch as processing - to avoid batch duplicates/collisions
            foreach ($webhooks as $webhook) {
                $webhook->update([
                    'webhooks_status' => 'processing',
                    'webhooks_started_at' => now(),
                ]);
            }

            //loop and process each webhook in the batch
            foreach ($webhooks as $webhook) {

                Log::info("stripe cronjob [subscription] - found valid webhook to process", ['process' => '[cronjob][stripe-subscription-uodate-trasnaction-id]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'webhook' => $webhook]);

                //update the transaction with corect ID
                \App\Models\Payment::where('payment_transaction_id', $webhook->webhooks_matching_reference)
                    ->update([
                        'payment_transaction_id' => $webhook->webhooks_payment_transactionid,
                    ]);

                //mark webhook cronjob as done
                $webhook->update([
                    'webhooks_status' => 'completed',
                    'webhooks_completed_at' => now(),
                ]);

            }
        }

        //[UPCOMING] update database for items marked as processing but never completed. Mark them as 'new'. Based on processing timestamp

    }
}