<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for recurring invocies
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\CloneInvoiceRepository;
use App\Repositories\EmailerRepository;
use App\Repositories\EventRepository;
use App\Repositories\EventTrackingRepository;
use App\Repositories\InvoiceGeneratorRepository;
use App\Repositories\UserRepository;
use Log;

class RecurringInvoiceRepository {

    /**
     * Clone invoice repository
     */
    protected $cloneinvoicerepo;

    /**
     * invoice model
     */
    protected $invoicemodel;

    /**
     * The event tracking repository instance.
     */
    protected $trackingrepo;

    /**
     * The event repository instance.
     */
    protected $eventrepo;

    /**
     * The invoice generator repository
     */
    protected $invoicegenerator;

    /**
     * Inject dependecies
     */
    public function __construct(
        CloneInvoiceRepository $cloneinvoicerepo,
        Invoice $invoicemodel,
        EventRepository $eventrepo,
        EventTrackingRepository $trackingrepo,
        UserRepository $userrepo,
        EmailerRepository $emailerrepo,
        InvoiceGeneratorRepository $invoicegenerator
    ) {

        $this->cloneinvoicerepo = $cloneinvoicerepo;
        $this->invoicemodel = $invoicemodel;
        $this->eventrepo = $eventrepo;
        $this->trackingrepo = $trackingrepo;
        $this->emailerrepo = $emailerrepo;
        $this->userrepo = $userrepo;
        $this->invoicegenerator = $invoicegenerator;

    }

    /**
     * find all invoices that need to be recurred today and recur them
     * @param numeric number of invoiced to get at a time. Set to 0 for unlimited
     * @return obj invoice collection
     */
    public function processInvoices($limit = 1) {

        Log::info("recurring invoice processing started", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //validate
        if (!is_numeric($limit)) {
            $limit = 1;
        }

        //do we have any invoices
        if (!$invoices = $this->getInvoices($limit)) {
            Log::info("no invoices recurring today were found", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return;
        }

        //invoice
        Log::info("applicable invoices were found - count (" . count($invoices) . ")", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //recur each invoice
        if (count($invoices) > 0) {
            $this->recurInvoices($invoices);
        }

        return $invoices;
    }

    /**
     * find all invoices that need to be recurred today, regardless billing cycle (daily, weekly etc)
     * @param numeric number of invoiced to get at a time. Set to 0 for unlimited
     * @return obj invoice collection
     */
    private function getInvoices($limit) {

        //todays date
        $today = \Carbon\Carbon::now()->format('Y-m-d');

        Log::info("searching for invoices that are due for renewal today ($today)", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);


        //new query
        $invoices = $this->invoicemodel->newQuery();

        // all fields
        $invoices->selectRaw('*');

        //next bill date is today
        $invoices->where('bill_recurring_next', $today);

        //recurring invoices
        $invoices->where('bill_recurring', 'yes');

        //exlcude those already processings
        $invoices->where('bill_cron_status', '!=', 'processing');

        //valid cycles
        $invoices->where('bill_recurring_duration', '>', 0);

        //there is still billing cycles to go
        $invoices->where(function ($query) {
            //infinite
            $query->where('bill_recurring_cycles', 0);
            //or still has cycles to go
            $query->orWhereColumn('bill_recurring_cycles_counter', '<', 'bill_recurring_cycles');
        });

        //exclude draft invoices
        $invoices->whereNotIn('bill_status', ['draft']);

        if ($limit == 0) {
            //return all rows
            return $invoices->get();
        } else {
            //return all rows
            return $invoices->take($limit)->get();
        }
    }

    /**
     * recur each invoice
     * @param object $invoices collection
     * @return object invoice collection
     */
    private function recurInvoices($invoices) {

        Log::info("starting to clone and recur the invoices", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //mark all invoices as processing, do avoid collisions
        foreach ($invoices as $invoice) {
            $invoice->bill_cron_status = 'processing';
            $invoice->bill_cron_date = now();
            $invoice->save();
        }

        //clone invoices one by one
        foreach ($invoices as $invoice) {

            //cloning data
            $data = [
                'invoice_id' => $invoice->bill_invoiceid,
                'client_id' => $invoice->bill_clientid,
                'project_id' => $invoice->bill_projectid,
                'invoice_date' => now(),
                'invoice_due_date' => now()->addDays(config('system.settings_invoices_recurring_grace_period')),
                'return' => 'object',
            ];
            if (!$child_invoice = $this->cloneinvoicerepo->clone($data)) {
                //mark as error
                $invoice->bill_cron_status = 'error';
                $invoice->save();
                //skip rest of tasks & log error
                Log::critical("the parent invoice could not cloned", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $invoice->bill_invoiceid]);
                continue;
            }

            //[parent invoice] update next due date
            switch ($invoice->bill_recurring_period) {
            case 'day':
                $invoice->bill_recurring_next = now()->addDays($invoice->bill_recurring_duration);
                break;

            case 'week':
                $invoice->bill_recurring_next = now()->addWeeks($invoice->bill_recurring_duration);
                break;

            case 'month':
                $invoice->bill_recurring_next = now()->addMonthsNoOverflow($invoice->bill_recurring_duration);
                break;

            case 'year':
                $invoice->bill_recurring_next = now()->addYearsNoOverflow($invoice->bill_recurring_duration);
                break;
            }

            //[parent invoice] update when last it was last recured (todays date)
            $invoice->bill_recurring_last = now();

            //[parent invoice] update cycles counter
            $invoice->bill_recurring_cycles_counter = $invoice->bill_recurring_cycles_counter + 1;
  
            //save parent invoice
            $invoice->save();

            //[child invoice] updates
            $child_invoice->bill_recurring_child = 'yes';
            $child_invoice->bill_recurring_parent_id = $invoice->bill_invoiceid;
            $child_invoice->bill_status = 'due';
            $child_invoice->save();

            //publish the new invoice & create timeline events
            $this->publishInvoice($child_invoice->bill_invoiceid, $invoice);

        }
    }

    /**
     * publish the new invoice. It will actually be added to email queue, which will processed by another cronjob
     * create timeline evemts and notifications
     * @param int $invoice_id invoice id
     * @param object $parent parent invoice
     * @return bool
     */
    private function publishInvoice($invoice_id, $parent) {

        Log::info("publishing new invoice - started", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $invoice_id]);

        //get the generated invoice
        if (!$payload = $this->invoicegenerator->generate($invoice_id)) {
            Log::critical("publishing new invoice failed - invoice could not be generated", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $invoice_id]);
            return false;
        }

        //invoice
        $invoice = $payload['bill'];

        /** ----------------------------------------------
         * record event [comment]
         * ----------------------------------------------*/
        $resource_id = (is_numeric($invoice->bill_projectid)) ? $invoice->bill_projectid : $invoice->bill_clientid;
        $resource_type = (is_numeric($invoice->bill_projectid)) ? 'project' : 'client';
        $data = [
            'event_creatorid' => 0, //created by 'system' user
            'event_item' => 'invoice',
            'event_item_id' => $invoice->bill_invoiceid,
            'event_item_lang' => 'event_created_invoice',
            'event_item_content' => __('lang.invoice') . ' - ' . $invoice->formatted_bill_invoiceid,
            'event_item_content2' => '',
            'event_parent_type' => 'invoice',
            'event_parent_id' => $invoice->bill_invoiceid,
            'event_parent_title' => $invoice->project_title,
            'event_clientid' => $invoice->bill_clientid,
            'event_show_item' => 'yes',
            'event_show_in_timeline' => 'yes',
            'eventresource_type' => $resource_type,
            'eventresource_id' => $resource_id,
            'event_notification_category' => 'notifications_billing_activity',
        ];
        //record event
        if ($event_id = $this->eventrepo->create($data)) {
            //get users (main client)
            $users = $this->userrepo->getClientUsers($invoice->bill_clientid, 'owner', 'ids');
            //record notification
            $emailusers = $this->trackingrepo->recordEvent($data, $users, $event_id);
        }

        /** ----------------------------------------------
         * send email [queued]
         * ----------------------------------------------*/
        if (isset($emailusers) && is_array($emailusers)) {
            //other data
            $data = [];
            //send to client users
            if ($users = \App\Models\User::WhereIn('id', $emailusers)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\PublishInvoice($user, $data, $invoice);
                    $mail->build();
                }
            }
        }

        //[parent invoice] update cron status
        $parent->bill_cron_status = 'completed';
        $parent->bill_cron_date = now();
        $parent->save();

        Log::info("publishing new invoice - completed", ['process' => '[recurring-invoices-cronjob]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $invoice_id]);

        return true;
    }

}