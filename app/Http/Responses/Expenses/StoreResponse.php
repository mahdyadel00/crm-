<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the expenses
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Expenses;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for expenses
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //prepend content on top of list or show full table
        if ($count == 1) {
            $html = view('pages/expenses/components/table/table', compact('expenses'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#expenses-table-wrapper',
                'action' => 'replace',
                'value' => $html);
        } else {
            
            //prepend content on top of list
            $html = view('pages/expenses/components/table/ajax', compact('expenses'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#expenses-td-container',
                'action' => 'prepend',
                'value' => $html);
        }

        //refresh stats
        if (isset($stats)) {
            $html = view('misc/list-pages-stats-content', compact('stats'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#list-pages-stats-widget',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        
        //show expense after adding
        if (request('ref') == 'quickadd' && request('show_after_adding') == 'on') {
            $jsondata['redirect_url'] = url("/expenses/v/" . $expense->expense_id);
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
