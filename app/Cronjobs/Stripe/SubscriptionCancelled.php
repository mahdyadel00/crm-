<?php

/** ---------------------------------------------------------------------------------------------------
 * [Stripe Complete Onetime]
 *
 *  - Cancels and subsccription that was initiated in teh stripe dashboard
 * @package    Grow CRM
 * @author     NextLoop
 *-----------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs\Stripe;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Log;

class SubscriptionCancelled {

    public function __invoke(
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        SubscriptionRepository $subscriptionrepo,
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
        $limit = 5;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_gateway_name', 'stripe')
            ->where('webhooks_type', 'customer.subscription.deleted')
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

                //get the subscription ID
                $subscription_gateway_id = $webhook->webhooks_matching_reference;

                Log::info("started process to cancel a subscription (ID: $subscription_gateway_id)", ['process' => '[cronjob][stripe-subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'webhook' => $webhook]);

                //get the subscription
                if (!$subscription = \App\Models\Subscription::Where('subscription_gateway_id', $subscription_gateway_id)->first()) {
                    Log::error("the subscription could not be found in the database", ['process' => '[cronjob][stripe-subscription-cancelled]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'webhook' => $webhook]);
                    //skip to next webhook in the batch
                    continue;
                }

                //check if subscrption is not already marked as 'cancelled' - skip
                if ($subscription->subscription_status == 'cancelled') {
                    $webhook->update([
                        'webhooks_status' => 'completed',
                        'webhooks_completed_at' => now(),
                    ]);
                    continue;
                }

                //update subscription
                $subscription->subscription_status = 'cancelled';
                $subscription->subscription_date_next_renewal = null;
                $subscription->save();

                /** ----------------------------------------------
                 * record event [comment]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => 1, //set to admin
                    'event_item' => 'subscription',
                    'event_item_id' => $subscription->subscription_id,
                    'event_item_lang' => 'event_cancelled_subscription',
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

                /** --------------------------------------------------------------------------
                 * send email [team] [queued]
                 * - invoice & payments users, with biling email preference enabled
                 * --------------------------------------------------------------------------*/
                $users = $userrepo->mailingListSubscriptions('email');
                foreach ($users as $user) {
                    //subscription started
                    $mail = new \App\Mail\SubscriptionCancelled($user, [], $subscription);
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

                if ($user = $userrepo->getClientAccountOwner($subscription->subscription_clientid)) {
                    //subscription started
                    $mail = new \App\Mail\SubscriptionCancelled($user, [], $subscription);
                    $mail->build();
                }

                //mark webhook cronjob as done
                $webhook->update([
                    'webhooks_status' => 'completed',
                    'webhooks_completed_at' => now(),
                ]);

            }

        }

    }
}