<?php

/** --------------------------------------------------------------------------------
 * This repository class generates a complete estimate object
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Repositories\EstimateRepository;
use App\Repositories\LineitemRepository;
use App\Repositories\TaxRepository;
use App\Repositories\UnitRepository;

class EstimateGeneratorRepository {

    /**
     * The estimate repository instance.
     */
    protected $estimaterepo;

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
        EstimateRepository $estimaterepo,
        TaxRepository $taxrepo,
        UnitRepository $unitrepo,
        LineitemRepository $lineitemrepo
    ) {

        $this->estimaterepo = $estimaterepo;
        $this->taxrepo = $taxrepo;
        $this->lineitemrepo = $lineitemrepo;
        $this->unitrepo = $unitrepo;

    }

    /**
     * generate and estimate and return the object payload
     * @param int $id record id
     * @return \Illuminate\Http\Response
     */
    public function generate($id = '') {

        //search for the estimate
        if (!$estimates = $this->estimaterepo->search($id)) {
            Log::error("estimate generation failed", ['process' => '[EstimateGeneratorRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'estimate_id' => $id]);
            return false;
        }

        //get the estimate
        $estimates = $this->estimaterepo->search($id, ['apply_filters' => false]);
        $bill = $estimates->first();

        //refresh estimate
        $this->estimaterepo->refreshEstimate($bill);

        //generic json payload
        $bill->json = $this->estimateJson($bill);

        //get tax rates
        $taxrates = $this->getTaxRates($bill);

        //tax html element - for popover
        $elements['tax_popover'] = htmlentities(view('pages/bill/components/elements/taxpopover', compact('bill', 'taxrates'))->render());

        //elements - discount popover
        $elements['discount_popover'] = htmlentities(view('pages/bill/components/elements/discounts', compact('bill'))->render());

        //elements - discount popover
        $elements['adjustments_popover'] = htmlentities(view('pages/bill/components/elements/adjustments', compact('bill'))->render());

        //taxes
        request()->merge([
            'taxresource_type' => 'estimate',
            'taxresource_id' => $id,
        ]);
        $taxes = $this->taxrepo->search();

        //line items
        request()->merge([
            'lineitemresource_type' => 'estimate',
            'lineitemresource_id' => $id,
        ]);
        $lineitems = $this->lineitemrepo->search();

        //reponse payload
        $payload = [
            'bill' => $this->estimateData($bill),
            'taxrates' => $taxrates,
            'units' => $this->unitrepo->search(),
            'taxes' => $bill->taxes,
            'lineitems' => $lineitems,
            'elements' => $elements,
        ];

        //return the invoice payload
        return $payload;
    }

    /**
     * return an array of 'enabled' tax rates, taking into account the system tax rates and
     * estimates own tax rates
     * @param object $estimate instance of the estimate model object
     * @return array
     */
    public function getTaxRates($estimate) {

        //estimates set tax rates
        $used = [];
        foreach ($estimate->taxes as $tax) {
            $used[] = $tax->tax_taxrateid;
        }

        //system tax rates
        $taxrates = \App\Models\Taxrate::get();
        foreach ($taxrates as $key => $taxrate) {
            //remove disabled taxes (which are not already used in this estimate)
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
     * additional estimate data
     *
     * @return array
     */
    private function estimateData($estimate) {

        //visibility of some elements
        $estimate->visibility_subtotal_row = ($estimate->bill_discount_amount > 0 || $estimate->bill_tax_total_amount > 0) ? '' : 'hidden';
        $estimate->visibility_discount_row = ($estimate->bill_discount_amount > 0) ? '' : 'hidden';
        $estimate->visibility_tax_row = ($estimate->bill_tax_total_amount > 0) ? '' : 'hidden';
        $estimate->visibility_before_tax_row = ($estimate->bill_discount_amount > 0) ? '' : 'hidden';
        $estimate->visibility_adjustment_row = ($estimate->bill_adjustment_amount > 0 || $estimate->bill_adjustment_amount < 0) ? '' : 'hidden';

        return $estimate;

    }

    /**
     * return a json string of specific fields that are needed in frontent
     *
     * @return array
     */
    private function estimateJson($estimate) {

        //visibility of some elements
        $visibility_subtotal_row = ($estimate->bill_discount_amount > 0 || $estimate->bill_tax_total_amount > 0) ? '' : 'hidden';
        $visibility_discount_row = ($estimate->bill_discount_amount > 0) ? '' : 'hidden';
        $visibility_tax_row = ($estimate->bill_tax_total_amount > 0) ? '' : 'hidden';

        $data = [
            'final_amount' => $estimate->bill_final_amount,
            'tax_type' => $estimate->bill_tax_type,
            'tax_amount' => $estimate->bill_tax_total_amount,
            'discount_type' => $estimate->bill_discount_type,
            'discount_amount' => $estimate->bill_discount_amount,
            'visibility_subtotal_row' => $visibility_subtotal_row,
            'visibility_discount_row' => $visibility_discount_row,
            'visibility_tax_row' => $visibility_tax_row,
        ];

        return json_encode($data);

    }

}