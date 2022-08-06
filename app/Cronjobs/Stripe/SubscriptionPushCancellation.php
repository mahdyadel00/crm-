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
use App\Repositories\StripeRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;
use Log;

class SubscriptionPushCancellation {

    public function __invoke(
        EventRepository $eventrepo,
        StripeRepository $striperepo,
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
        $limit = 1;
        if ($webhooks = \App\Models\Webhook::Where('webhooks_gateway_name', 'stripe')
            ->where('webhooks_type', 'crm-subscription-cancellation')
            ->where('webhooks_attempts', '<', 5)
            ->whereIn('webhooks_status', ['new', 'retry'])->take($limit)->get()) {

            //log
            Log::info("found applicable webhooks", ['process' => '[cronjob][stripe-subscription-push-cancellation]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $webhooks]);

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

                //does the subscription exist
                if (!$subscription = $striperepo->getSubscription($subscription_gateway_id)) {
                    $webhook->update([
                        'webhooks_attempts' => $webhook->webhooks_attempts + 1,
                        'webhooks_status' => 'retry',
                        'webhooks_started_at' => now(),
                    ]);
                    continue;
                }

                //cancel at stripe
                if ($subscription->status != 'canceled') {
                    if (!$striperepo->cancelSubscription($subscription_gateway_id)) {
                        $webhook->update([
                            'webhooks_attempts' => $webhook->webhooks_attempts + 1,
                            'webhooks_status' => 'retry',
                            'webhooks_started_at' => now(),
                        ]);
                        continue;
                    }
                }

                //get subscription model (ours) from payload json
                $subscription = json_decode($webhook->webhooks_payload);

                //log this event
                $log = new \App\Models\Log();
                $log->log_creatorid = $webhook->webhooks_creatorid;
                $log->log_text = 'event_cancelled_the_subscription';
                $log->log_text_type = 'lang';
                $log->log_payload = '';
                $log->logresource_type = 'subscription';
                $log->logresource_id = $subscription->subscription_id;
                $log->save();

                /** ----------------------------------------------
                 * record event [comment]
                 * ----------------------------------------------*/
                $data = [
                    'event_creatorid' => 0,
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
                 * send email [client] [queued]
                 * - thank you email to user who placed order
                 * - subscription has started
                 * --------------------------------------------------------------------------*/

                if ($user = $userrepo->getClientAccountOwner($subscription->subscription_clientid)) {
                    //subscription started
                    $mail = new \App\Mail\SubscriptionCancelled($user, [], $subscription);
                    $mail->build();
                }
                //track event
                if (is_numeric($event_id)) {
                    $trackingrepo->recordEvent($data, $user, $event_id);
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