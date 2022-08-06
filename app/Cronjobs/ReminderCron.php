<?php

/** -------------------------------------------------------------------------------------------------
 * TEMPLATE
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use Log;

class ReminderCron {

    public function __invoke(

    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //log that its run
        //Log::info("Cronjob has started", ['process' => '[reminder-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //process 50 at a time
        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        $reminders = \App\Models\Reminder::Where('reminder_datetime', '<', $now)
            ->where('reminder_status', 'active')
            ->take(50)->get();

        Log::info("Cronjob date - $now", ['process' => '[reminder-cron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //process each reminder
        foreach ($reminders as $reminder) {

            //queue email
            if ($user = \App\Models\User::Where('id', $reminder->reminder_userid)->first()) {

                //default
                $data = [];
                $data['linked_item_name'] = '';

                //get meta data for the linked item - [CLIENTS]
                if ($reminder->reminderresource_type == 'client') {
                    //meta data
                    $data['linked_item_type'] = __('lang.client');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.client');
                    //create other meta data
                    if ($client = \App\Models\Client::Where('client_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $client->client_company_name;
                        $data['linked_item_url'] = url('/clients/' . $client->client_id);
                        //primary user
                        if ($user = \App\Models\User::Where('clientid', $client->client_id)->where('account_owner', 'yes')->first()) {
                            $data['linked_item_name'] = $user->first_name . ' ' . $user->last_name;
                        }
                    } else {
                        $data['linked_item_title'] = '---';
                        $data['linked_item_url'] = url('/clients/');
                    }
                }

                //get meta data for the linked item - [PROJECTS]
                if ($reminder->reminderresource_type == 'project') {
                    //meta data
                    $data['linked_item_type'] = __('lang.project');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.project');
                    //create other meta data
                    if ($project = \App\Models\Project::Where('project_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $project->project_title;
                        $data['linked_item_url'] = url('/projects/' . $project->project_id);
                    } else {
                        $data['linked_item_title'] = __('lang.project');
                        $data['linked_item_url'] = url('/projects/');
                    }
                }

                //get meta data for the linked item - [TASKS]
                if ($reminder->reminderresource_type == 'task') {
                    //meta data
                    $data['linked_item_type'] = __('lang.task');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.task');
                    //create other meta data
                    if ($task = \App\Models\Task::Where('task_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $task->task_title;
                        $data['linked_item_url'] = url("/tasks/v/" . $task->task_id . "/" . str_slug($task->task_title));
                    } else {
                        $data['linked_item_title'] = __('lang.task');
                        $data['linked_item_url'] = url('/tasks/');
                    }
                }

                //get meta data for the linked item - [LEADS]
                if ($reminder->reminderresource_type == 'lead') {
                    //meta data
                    $data['linked_item_type'] = __('lang.lead');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.lead');
                    //create other meta data
                    if ($lead = \App\Models\Lead::Where('lead_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $lead->lead_title;
                        $data['linked_item_url'] = url("/leads/v/" . $lead->lead_id . "/" . str_slug($lead->lead_title));
                        $data['linked_item_name'] = $lead->lead_firstname . ' ' . $lead->lead_lastname;
                    } else {
                        $data['linked_item_title'] = __('lang.lead');
                        $data['linked_item_url'] = url('/leads/');
                    }
                }

                //get meta data for the linked item - [INVOICES]
                if ($reminder->reminderresource_type == 'invoice') {
                    //meta data
                    $data['linked_item_type'] = __('lang.invoice');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.invoice');
                    //create other meta data
                    if ($invoice = \App\Models\Invoice::Where('bill_invoiceid', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $invoice->formatted_bill_invoiceid;
                        $data['linked_item_url'] = url('/invoices/' . $invoice->bill_invoiceid);
                    } else {
                        $data['linked_item_title'] = __('lang.invoice');
                        $data['linked_item_url'] = url('/invoices/');
                    }
                }

                //get meta data for the linked item - [ESTIMATE]
                if ($reminder->reminderresource_type == 'estimate') {
                    //meta data
                    $data['linked_item_type'] = __('lang.estimate');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.estimate');
                    //create other meta data
                    if ($estimate = \App\Models\Estimate::Where('bill_estimateid', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $estimate->formatted_bill_estimateid;
                        $data['linked_item_url'] = url('/estimates/' . $estimate->bill_estimateid);
                    } else {
                        $data['linked_item_title'] = __('lang.estimate');
                        $data['linked_item_url'] = url('/estimates/');
                    }
                }

                //get meta data for the linked item - [TICKETS]
                if ($reminder->reminderresource_type == 'ticket') {
                    //meta data
                    $data['linked_item_type'] = __('lang.support_ticket');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.support_ticket');
                    //create other meta data
                    if ($ticket = \App\Models\Ticket::Where('ticket_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $ticket->ticket_subject;
                        $data['linked_item_url'] = url('/tickets/' . $ticket->ticket_id);
                    } else {
                        $data['linked_item_title'] = __('lang.support_ticket');
                        $data['linked_item_url'] = url('/tickets/');
                    }
                }

                //get meta data for the linked item - [SUBSCRIPTION]
                if ($reminder->reminderresource_type == 'subscription') {
                    //meta data
                    $data['linked_item_type'] = __('lang.subscription');
                    $data['linked_item_id'] = $reminder->reminderresource_id;
                    $data['linked_item_name'] = __('lang.subscription');
                    //create other meta data
                    if ($subscription = \App\Models\Subscription::Where('subscription_id', $reminder->reminderresource_id)->first()) {
                        $data['linked_item_title'] = $subscription->formatted_subscriptionid;
                        $data['linked_item_url'] = url('/subscriptions/' . $subscription->subscription_id);
                    } else {
                        $data['linked_item_title'] = __('lang.subscription');
                        $data['linked_item_url'] = url('/subscriptions/');
                    }
                }

                //update reminder as due
                $reminder->reminder_status = 'due';
                $reminder->save();

                //send email
                $mail = new \App\Mail\Reminder($user, $data, $reminder);
                $mail->build();

            } else {
                //delete this reminder
                $reminder->delete();
            }
        }

        //reset last cron run data
        \App\Models\Settings::where('settings_id', 1)
            ->update([
                'settings_cronjob_has_run' => 'yes',
                'settings_cronjob_last_run' => now(),
            ]);

    }

}