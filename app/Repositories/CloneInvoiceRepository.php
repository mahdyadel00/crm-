<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for cloning invoices
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

/** --------------------------------------------------------------------------
 * [Clone Invoice Repository]
 * Clone an invoice. The new invoice is set to 'draft status' by default
 * It can be published as needed
 * @source Nextloop
 *--------------------------------------------------------------------------*/
namespace App\Repositories;

use App\Repositories\InvoiceGeneratorRepository;
use App\Repositories\InvoiceRepository;
use Log;

class CloneInvoiceRepository {

    /**
     * The invoice repo instance
     */
    protected $invoicerepo;

    /**
     * The invoice generator instance
     */
    protected $invoicegenerator;

    /**
     * Inject dependecies
     */
    public function __construct(InvoiceRepository $invoicerepo, InvoiceGeneratorRepository $invoicegenerator) {
        $this->invoicerepo = $invoicerepo;
        $this->invoicegenerator = $invoicegenerator;
    }

    /**
     * Clone an invoice
     * @param array data array
     *              - invoice_id
     *              - client_id
     *              - project_id
     *              - invoice_date
     *              - invoice_due_date
     *              - return (id|object)
     * @return mixed int|object
     */
    public function clone ($data = []) {

        //info
        Log::info("cloning invoice started", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);

        //validate information
        if (!$this->validateData($data)) {
            Log::info("cloning invoice failed", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get the invoice via the invoice generator
        if (!$payload = $this->invoicegenerator->generate($data['invoice_id'])) {
            Log::error("an invoice with this invoice id, could not be loaded", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $data['invoice_id']]);
            return false;
        }

        //get clean invoice object for cloning
        $invoice = \App\Models\Invoice::Where('bill_invoiceid', $data['invoice_id'])->first();

        //clone main invoice
        $new_invoice = $invoice->replicate();

        //update new invoice with specified data
        $new_invoice->bill_clientid = $data['client_id'];
        $new_invoice->bill_date = $data['invoice_date'];
        $new_invoice->bill_created = now();
        $new_invoice->bill_updated = now();
        $new_invoice->bill_due_date = $data['invoice_due_date'];
        $new_invoice->bill_projectid = $data['project_id'];
        $new_invoice->bill_visibility = 'hidden';
        $new_invoice->bill_status = 'draft';

        //[cleanup] remove recurring and other unwanted data, inherited from parent
        $new_invoice->bill_recurring = 'no';
        $new_invoice->bill_recurring_duration = null;
        $new_invoice->bill_recurring_period = null;
        $new_invoice->bill_recurring_cycles = null;
        $new_invoice->bill_recurring_cycles_counter = null;
        $new_invoice->bill_recurring_last = null;
        $new_invoice->bill_recurring_next = null;
        $new_invoice->bill_recurring_child = 'no';
        $new_invoice->bill_recurring_parent_id = null;
        $new_invoice->bill_overdue_reminder_sent = 'no';
        $new_invoice->bill_date_sent_to_customer = null;
        $new_invoice->bill_notes = '';
        $new_invoice->bill_cron_status = 'none';
        $new_invoice->bill_cron_date = null;

        //save
        $new_invoice->save();

        //replicate each line item
        foreach ($payload['lineitems'] as $lineitem_x) {

            //get clean lineitem object for cloning
            if (!$lineitem = \App\Models\LineItem::Where('lineitem_id', $lineitem_x->lineitem_id)->first()) {
                //skip it
                $continue;
            }

            //clone line
            $new_lineitem = $lineitem->replicate();
            $new_lineitem->lineitemresource_id = $new_invoice->bill_invoiceid;
            $new_lineitem->save();

            //clone line tax rates
            foreach ($payload['taxes'] as $tax_x) {
                //get clean tax item for cloning
                if ($tax = \App\Models\Tax::Where('tax_id', $tax_x->tax_id)->first()) {
                    if ($tax->taxresource_type == 'lineitem' && $tax->taxresource_id == $lineitem->lineitem_id) {
                        $new_tax = $tax->replicate();
                        $new_tax->taxresource_id = $new_lineitem->lineitem_id;
                        $new_tax->save();
                    }
                }
            }
        }

        //replicate main invoices tax items
        foreach ($payload['taxes'] as $tax) {
            //get clean tax item for cloning
            if ($tax = \App\Models\Tax::Where('tax_id', $tax_x->tax_id)->first()) {
                if ($tax->taxresource_type == 'invoice') {
                    $new_tax = $tax->replicate();
                    $new_tax->taxresource_id = $new_invoice->bill_invoiceid;
                    $new_tax->save();
                }
            }
        }

        //finished, make invoice visible
        $new_invoice->bill_visibility = 'visible';
        $new_invoice->save();


        Log::info("cloning invoice completed", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'new_invoice_id' => $new_invoice->bill_invoiceid]);

        //return invoice id | invoice object
        if (isset($data['return']) && $data['return'] == 'id') {
            return $new_invoice->bill_invoiceid;
        } else {
            return $new_invoice;
        }
    }

    /**
     * validate required data for cloning an invoice
     * @param array $data information payload
     * @return bool
     */
    private function validateData($data = []) {

        //validation
        if (!isset($data['invoice_id']) || !isset($data['client_id']) || !isset($data['invoice_date']) || !isset($data['invoice_due_date'])) {
            Log::error("the supplied data is not valid", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //invoice id
        if (!is_numeric($data['invoice_id'])) {
            Log::error("the supplied invoice id is invalid", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $data['invoice_id']]);
            return false;
        }

        //client id
        if (!is_numeric($data['client_id'])) {
            Log::error("the supplied client id is invalid", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'client_id' => $data['client_id']]);
            return false;
        }

        //check client exists
        if (!$client = \App\Models\Client::Where('client_id', $data['client_id'])->first()) {
            Log::error("the specified client could not be found", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => $data['project_id']]);
            return false;
        }

        //check project exists
        if (isset($data['project_id']) && is_numeric($data['project_id'])) {
            if (!$project = \App\Models\Project::Where('project_id', $data['project_id'])->first()) {
                Log::error("the specified project could not be found", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => $data['project_id']]);
                return false;
            }
        }

        //check client and project match
        if (isset($data['project_id']) && is_numeric($data['project_id'])) {
            if ($project->project_clientid != $client->client_id) {
                Log::error("the specified client & project do not match", ['process' => '[CloneInvoiceRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => $data['project_id']]);
                return false;
            }
        }

        return true;
    }

}