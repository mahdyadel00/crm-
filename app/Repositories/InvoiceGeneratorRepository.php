<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for estimates
 * [Generate Invoice]
 * All the logic and queries for fetching an invoice. this allows a complete
 * invoice to be called from inside any other controller/cornjob.
 *
 * The returned payload will contain all the data needed to show a web inovice
 * or to generate a PDF invoice.
 *
 * [NOTE] This is not generating a PDF invoice, just the invoice object and
 * all related lineitems, payments etc
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Repositories\InvoiceRepository;
use App\Repositories\LineitemRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UnitRepository;
use Log;

class InvoiceGeneratorRepository {

    /**
     * The invoice repository instance.
     */
    protected $invoicerepo;

    /**
     * The tax repository instance.
     */
    protected $taxrepo;

    /**
     * The line item repository instance.
     */
    protected $lineitemrepo;

    /**
     * The unit item repository instance.
     */
    protected $unitrepo;

    /**
     * Inject dependecies
     */
    public function __construct(
        InvoiceRepository $invoicerepo,
        TaxRepository $taxrepo,
        UnitRepository $unitrepo,
        LineitemRepository $lineitemrepo
    ) {

        $this->invoicerepo = $invoicerepo;
        $this->taxrepo = $taxrepo;
        $this->lineitemrepo = $lineitemrepo;
        $this->unitrepo = $unitrepo;

    }

    /**
     * generate and invoice and return an array that contains the following
     *       - The invoice object
     *       - tax rates
     *       - line items
     *       - elements
     *       - units
     * @param int $id invoice id
     * @return array
     */
    public function generate($id = '') {

        //info
        Log::info("invoice generation started", ['process' => '[InvoiceGeneratorRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $id]);

        //search for the invoice
        $invoices = $this->invoicerepo->search($id, ['apply_filters' => false]);

        //get the invoice
        if (!$bill = $invoices->first()) {
            Log::error("invoice generation failed", ['process' => '[InvoiceGeneratorRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $id]);
            return false;
        }

        //refresh invoice
        $this->invoicerepo->refreshInvoice($bill);

        //generic json payload
        $bill->json = $this->invoiceJson($bill);

        //get tax rates
        $taxrates = $this->getTaxRates($bill);

        //tax html element - for popover
        $elements['tax_popover'] = htmlentities(view('pages/bill/components/elements/taxpopover', compact('bill', 'taxrates'))->render());

        //elements - discount popover
        $elements['discount_popover'] = htmlentities(view('pages/bill/components/elements/discounts', compact('bill'))->render());

        //elements - discount popover
        $elements['adjustments_popover'] = htmlentities(view('pages/bill/components/elements/adjustments', compact('bill'))->render());

        //get taxes
        request()->merge([
            'taxresource_type' => 'invoice',
            'taxresource_id' => $id,
        ]);
        $taxes = $this->taxrepo->search();

        //line items
        request()->merge([
            'lineitemresource_type' => 'invoice',
            'lineitemresource_id' => $id,
        ]);
        $lineitems = $this->lineitemrepo->search();

        //reponse payload
        $payload = [
            'bill' => $this->invoiceData($bill),
            'taxrates' => $taxrates,
            'units' => $this->unitrepo->search(),
            'taxes' => $bill->taxes,
            'lineitems' => $lineitems,
            'elements' => $elements,
        ];

        //info
        Log::info("invoice generated successfully", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

        //return the invoice payload
        return $payload;
    }

    /**
     * return an array of 'enabled' tax rates, taking into account the system tax rates and
     * invoices own tax rates
     * @param object $invoice instance of the invoice model object
     * @return array
     */
    public function getTaxRates($invoice) {

        //invoices set tax rates
        $used = [];
        foreach ($invoice->taxes as $tax) {
            $used[] = $tax->tax_taxrateid;
        }

        //system tax rates
        $taxrates = \App\Models\Taxrate::get();
        foreach ($taxrates as $key => $taxrate) {
            //remove disabled taxes (which are not already used in this invoice)
            if ($taxrate->taxrate_status == 'disabled' && !in_array($taxrate->taxrate_id, $used)) {
                $taxrates->forget($key);
            }
            //mark selected
            if (in_array($taxrate->taxrate_id, $used)) {
                $taxrate->taxrate_selected = 'selected';
            }
        }

        return $taxrates;
    }

    /**
     * additional invoice data
     * @param object $invoice instance of the invoice model object
     * @return array
     */
    private function invoiceData($invoice) {

        //visibility of some elements
        $invoice->visibility_subtotal_row = ($invoice->bill_discount_amount > 0 || $invoice->bill_tax_total_amount > 0) ? '' : 'hidden';
        $invoice->visibility_discount_row = ($invoice->bill_discount_amount > 0) ? '' : 'hidden';
        $invoice->visibility_tax_row = ($invoice->bill_tax_total_amount > 0) ? '' : 'hidden';
        $invoice->visibility_before_tax_row = ($invoice->bill_discount_amount > 0) ? '' : 'hidden';
        $invoice->visibility_adjustment_row = ($invoice->bill_adjustment_amount > 0 || $invoice->bill_adjustment_amount < 0) ? '' : 'hidden';


        return $invoice;

    }

    /**
     * return a json string of specific fields that are needed in frontent
     * @param object $invoice instance of the invoice model object
     * @return array
     */
    private function invoiceJson($invoice) {

        //visibility of some elements
        $visibility_subtotal_row = ($invoice->bill_discount_amount > 0 || $invoice->bill_tax_total_amount > 0) ? '' : 'hidden';
        $visibility_discount_row = ($invoice->bill_discount_amount > 0) ? '' : 'hidden';
        $visibility_tax_row = ($invoice->bill_tax_total_amount > 0) ? '' : 'hidden';

        $data = [
            'final_amount' => $invoice->bill_final_amount,
            'tax_type' => $invoice->bill_tax_type,
            'tax_amount' => $invoice->bill_tax_total_amount,
            'discount_type' => $invoice->bill_discount_type,
            'discount_amount' => $invoice->bill_discount_amount,
            'visibility_subtotal_row' => $visibility_subtotal_row,
            'visibility_discount_row' => $visibility_discount_row,
            'visibility_tax_row' => $visibility_tax_row,
        ];

        return json_encode($data);

    }

}