<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for deleting records
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Illuminate\Support\Facades\Storage;
use Log;

class DestroyRepository {

    /**
     * destroy a project and all related items
     * @param int $project_id project id
     * @return bool or id of record
     */
    public function destroyProject($project_id) {

        //validate project
        if (!is_numeric($project_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get project and validate
        if (!$project = \App\Models\Project::Where('project_id', $project_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete payments
        $project->payments()->delete();

        //delete contracts
        $project->contracts()->delete();

        //delete tags
        $project->tags()->delete();

        //delete milestones
        $project->milestones()->delete();

        //delete notes
        $project->notes()->delete();

        //delete timers
        $project->timers()->delete();

        //delete assigned table records
        $project->assignedrecords()->delete();

        //delete manager table records
        $project->managerrecords()->delete();

        //delete estimates and associated item
        if ($estimates = $project->estimates()->get()) {
            foreach ($estimates as $estimate) {
                $this->destroyEstimate($estimate->bill_estimateid);
            }
        }

        //delete invoices and associated item
        if ($invoices = $project->invoices()->get()) {
            foreach ($invoices as $invoice) {
                $this->destroyInvoice($invoice->bill_invoiceid);
            }
        }

        //delete expenses and associated item
        if ($expenses = $project->expenses()->get()) {
            foreach ($expenses as $expense) {
                $this->destroyExpense($expense->expense_id);
            }
        }

        //delete comments
        if ($comments = $project->comments()->get()) {
            foreach ($comments as $comment) {
                $this->destroyComment($comment->comment_id);
            }
        }

        //delete events & events tracking
        if ($events = $project->events()->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete files
        if ($files = $project->files()->get()) {
            foreach ($files as $file) {
                $this->destroyFile($file->file_id);
            }
        }

        //delete tasks
        if ($tasks = $project->tasks()->get()) {
            foreach ($tasks as $task) {
                $this->destroyTask($task->task_id);
            }
        }

        //delete tickets
        if ($tickets = $project->tickets()->get()) {
            foreach ($tickets as $ticket) {
                $this->destroyTicket($ticket->ticket_id);
            }
        }

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'project')->Where('emailqueue_resourceid', $project->project_id)->delete();

        //delete the project
        $project->delete();

        return true;

    }

    /**
     * destroy a client and all related items
     * @param numeric $client_id
     * @return bool or id of record
     */
    public function destroyClient($client_id) {

        //validate client
        if (!is_numeric($client_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][client]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get client and validate
        if (!$client = \App\Models\Client::Where('client_id', $client_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][client]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete contracts
        $client->contracts()->delete();

        //delete estimates and associated item
        if ($estimates = $client->estimates()->get()) {
            foreach ($estimates as $estimate) {
                $this->destroyEstimate($estimate->bill_estimateid);
            }
        }

        //delete expenses and associated item
        if ($expenses = $client->expenses()->get()) {
            foreach ($expenses as $expense) {
                $this->destroyExpense($expense->expense_id);
            }
        }

        //delete projects and associated item
        if ($projects = $client->projects()->get()) {
            foreach ($projects as $project) {
                $this->destroyProject($project->project_id);
            }
        }

        //delete tickets
        if ($tickets = $client->tickets()->get()) {
            foreach ($tickets as $ticket) {
                $this->destroyTicket($ticket->ticket_id);
            }
        }

        //delete proposals
        \App\Models\Proposal::Where('doc_client_id', $client_id)->getQuery()->delete();

        //delete contracts
        \App\Models\Contract::Where('doc_client_id', $client_id)->getQuery()->delete();

        //delete proposals
        if ($proposals = $client->proposals()->get()) {
            foreach ($proposals as $proposal) {
                $this->destroyProposal($proposal->doc_id);
            }
        }

        //delete contracts
        if ($contracts = $client->contracts()->get()) {
            foreach ($contracts as $contract) {
                $this->destroyContract($contract->doc_id);
            }
        }

        //delete users
        $client->users()->delete();

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'client')->Where('emailqueue_resourceid', $client->client_id)->delete();

        //delete client
        $client->delete();

        return true;
    }

    /**
     * destroy a ticket and all related items
     * @param numeric $ticket_id
     * @return bool or id of record
     */
    public function destroyTicket($ticket_id) {

        //validate ticket
        if (!is_numeric($ticket_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][ticke]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get ticket and validate
        if (!$ticket = \App\Models\Ticket::Where('ticket_id', $ticket_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][ticket]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete attachemnts
        if ($attachments = $ticket->attachments()->get()) {
            foreach ($attachments as $attachment) {
                if ($attachment->attachment_directory != '') {
                    if (Storage::exists("files/$attachment->attachment_directory")) {
                        Storage::deleteDirectory("files/$attachment->attachment_directory");
                    }
                }
                $attachment->delete();
            }
        }

        //delete replies and their attachments
        if ($replies = $ticket->replies()->get()) {
            foreach ($replies as $reply) {
                //each attachments - delete physically & from DB
                if ($attachments = $reply->attachments()->get()) {
                    foreach ($attachments as $attachment) {
                        if ($attachment->attachment_directory != '') {
                            if (Storage::exists("files/$attachment->attachment_directory")) {
                                Storage::deleteDirectory("files/$attachment->attachment_directory");
                            }
                        }
                        $attachment->delete();
                    }
                }
                $reply->delete();
            }
        }

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'ticket')->Where('emailqueue_resourceid', $ticket_id)->delete();

        //delete events
        \App\Models\Event::Where('event_parent_type', 'ticket')->Where('event_parent_id', $ticket_id)->delete();

        //delete client
        $ticket->delete();
    }

    /**
     * destroy a task and all related items
     * @param numeric $task_id
     * @return bool or id of record
     */
    public function destroyTask($task_id) {

        //validate task
        if (!is_numeric($task_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][task]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get task and validate
        if (!$task = \App\Models\Task::Where('task_id', $task_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][task]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete tags
        $task->tags()->delete();

        //delete checklists
        $task->checklists()->delete();

        //delete assigned records
        $task->assignedrecords()->delete();

        //delete attachemnts
        if ($attachments = $task->attachments()->get()) {
            foreach ($attachments as $attachment) {
                if ($attachment->attachment_directory != '') {
                    if (Storage::exists("files/$attachment->attachment_directory")) {
                        Storage::deleteDirectory("files/$attachment->attachment_directory");
                    }
                }
                $attachment->delete();
            }
        }

        //delete timers
        $task->timers()->delete();

        //delete comments
        if ($comments = $task->comments()->get()) {
            foreach ($comments as $comment) {
                $this->destroyComment($comment->comment_id);
            }
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_parent_type', 'task')->Where('event_parent_id', $task_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'task')->Where('emailqueue_resourceid', $task_id)->delete();

        //delete task
        $task->delete();
    }

    /**
     * destroy a expense and all related items
     * @param numeric $expense_id id of the record
     * @return null
     */
    public function destroyExpense($expense_id) {

        //validate expense
        if (!is_numeric($expense_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][expense]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get expense and validate
        if (!$expense = \App\Models\Expense::Where('expense_id', $expense_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][expense]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete attachemnts
        if ($attachments = $expense->attachments()->get()) {
            foreach ($attachments as $attachment) {
                if ($attachment->attachment_directory != '') {
                    if (Storage::exists("files/$attachment->attachment_directory")) {
                        Storage::deleteDirectory("files/$attachment->attachment_directory");
                    }
                }
                $attachment->delete();
            }
        }

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'expense')->Where('emailqueue_resourceid', $expense_id)->delete();

        //delete expense
        $expense->delete();

    }

    /**
     * destroy any type of comment
     * @param numeric $comment_id id of the record
     * @return null
     */
    public function destroyComment($comment_id) {

        //validate comment
        if (!is_numeric($comment_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][comment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get file and validate
        if (!$comment = \App\Models\Comment::Where('comment_id', $comment_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][comment]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_item', 'comment')->Where('event_item_id', $comment_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete comment
        $comment->delete();
    }

    /**
     * destroy any type of file
     * @param numeric $file_id
     * @return bool or id of record
     */
    public function destroyFile($file_id) {

        //validate file
        if (!is_numeric($file_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][file]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get file and validate
        if (!$file = \App\Models\File::Where('file_id', $file_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][file]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //physically delete directory
        if ($file->file_directory != '') {
            if (Storage::exists("files/$file->file_directory")) {
                Storage::deleteDirectory("files/$file->file_directory");
            }
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_item', 'file')->Where('event_item_id', $file_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete file
        $file->delete();
    }

    /**
     * destroy a invoice and all related items
     * @param numeric $invoice_id
     * @return bool or id of record
     */
    public function destroyInvoice($invoice_id) {

        //validate invoice
        if (!is_numeric($invoice_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][invoice]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get invoice and validate
        if (!$invoice = \App\Models\Invoice::Where('bill_invoiceid', $invoice_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][invoice]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete payments
        $invoice->payments()->delete();

        //delete line items
        $invoice->lineitems()->delete();

        //update linked expenses to 'not invoiced'
        \App\Models\Expense::where('expense_billable_invoiceid', $invoice_id)->update([
            'expense_billing_status' => 'not_invoiced',
            'expense_billable_invoiceid' => NULL,
        ]);

        //update linked timers to 'not invoiced'
        \App\Models\Timer::where('timer_billing_invoiceid', $invoice_id)->update([
            'timer_billing_status' => 'not_invoiced',
            'timer_billing_invoiceid' => NULL,
        ]);

        //delete events
        \App\Models\Event::Where('event_parent_type', 'invoice')->where('event_parent_id', $invoice_id)->delete();
        \App\Models\EventTracking::Where('parent_type', 'invoice')->where('parent_id', $invoice_id)->delete();

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'invoice')->Where('emailqueue_resourceid', $invoice_id)->delete();

        //delete invoice
        $invoice->delete();

    }

    /**
     * destroy a estimate and all related items
     * @param numeric $estimate_id
     * @return bool or id of record
     */
    public function destroyEstimate($estimate_id) {

        //validate estimate
        if (!is_numeric($estimate_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][estimate]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get estimate and validate
        if (!$estimate = \App\Models\Estimate::Where('bill_estimateid', $estimate_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][estimate]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete line items
        $estimate->lineitems()->delete();

        //delete events
        \App\Models\Event::Where('event_parent_type', 'estimate')->where('event_parent_id', $estimate_id)->delete();
        \App\Models\EventTracking::Where('parent_type', 'estimate')->where('parent_id', $estimate_id)->delete();

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'estimate')->Where('emailqueue_resourceid', $estimate_id)->delete();

        //delete estimate
        $estimate->delete();
    }

    /**
     * destroy a lead and all related items
     * @param numeric $lead_id
     * @return bool or id of record
     */
    public function destroyLead($lead_id) {

        //validate lead
        if (!is_numeric($lead_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get lead and validate
        if (!$lead = \App\Models\lead::Where('lead_id', $lead_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][lead]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete tags
        $lead->tags()->delete();

        //delete checklists
        $lead->checklists()->delete();

        //delete assigned records
        $lead->assignedrecords()->delete();

        //delete attachemnts
        if ($attachments = $lead->attachments()->get()) {
            foreach ($attachments as $attachment) {
                if ($attachment->attachment_directory != '') {
                    if (Storage::exists("files/$attachment->attachment_directory")) {
                        Storage::deleteDirectory("files/$attachment->attachment_directory");
                    }
                }
                $attachment->delete();
            }
        }

        //delete comments
        if ($comments = $lead->comments()->get()) {
            foreach ($comments as $comment) {
                $this->destroyComment($comment->comment_id);
            }
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_parent_type', 'lead')->Where('event_parent_id', $lead_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        if ($events = \App\Models\Event::Where('event_parent_type', 'lead')->Where('event_parent_id', $lead_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete proposals
        if ($proposals = $lead->proposals()->get()) {
            foreach ($proposals as $proposal) {
                $this->destroyProposal($proposal->doc_id);
            }
        }

        //delete queued emails
        \App\Models\EmailQueue::Where('emailqueue_resourcetype', 'lead')->Where('emailqueue_resourceid', $lead_id)->delete();

        //delete lead
        $lead->delete();

    }

    /**
     * destroy any type of subscription
     * @param numeric $id id of the record
     * @return null
     */
    public function destroySubscription($id) {

        //validate comment
        if (!is_numeric($id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get subscription and validate
        if (!$subscription = \App\Models\Subscription::Where('subscription_id', $id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][subscription]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_item', 'subscription')->Where('event_item_id', $id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete invoices & payments
        if ($invoices = \App\Models\Invoice::Where('bill_subscriptionid', $id)->get()) {
            foreach ($invoices as $invoice) {
                $this->destroyInvoice($invoice->bill_invoiceid);
            }
        }

        //delete comment
        $subscription->delete();
    }

    /**
     * destroy any type of proposal
     * @param numeric $doc_id id of the record
     * @return null
     */
    public function destroyProposal($doc_id) {

        //validate proposal
        if (!is_numeric($doc_id)) {
            Log::error("validation error - invalid params", ['process' => '[destroy][proposal]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //get file and validate
        if (!$proposal = \App\Models\Proposal::Where('doc_id', $doc_id)->first()) {
            Log::error("record could not be found", ['process' => '[destroy][proposal]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //delete events and events tracking
        if ($events = \App\Models\Event::Where('event_item', 'proposal')->Where('event_item_id', $doc_id)->get()) {
            foreach ($events as $event) {
                $event->trackings()->delete();
                $event->delete();
            }
        }

        //delete proposal
        $proposal->delete();
    }

}