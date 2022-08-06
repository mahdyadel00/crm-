<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [attach] process for the expenses
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Expenses;
use Illuminate\Contracts\Support\Responsable;

class AttachDettachUpdateResponse implements Responsable {

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

        //expense id
        $id = $expenses->first()->expense_id;

        //we dettached [client or project] from regular expenses list page
        if (!request()->filled('expenseresource_type')) {
            $html = view('pages/expenses/components/table/ajax', compact('expenses'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#expense_$id",
                'action' => 'replace-with',
                'value' => $html);
        }

        //we dettached [client] from an embedded [client] page
        if (request('expenseresource_type') == 'client' && !is_numeric(request('expense_clientid'))) {
            $jsondata['dom_visibility'][] = array(
                'selector' => "#expense_$id",
                'action' => 'slideup-slow-remove',
            );
        }

        //we dettached [project] from an embedded [project] page
        if (request('expenseresource_type') == 'project' && !is_numeric(request('expense_projectid'))) {
            $jsondata['dom_visibility'][] = array(
                'selector' => "#expense_$id",
                'action' => 'slideup-slow-remove',
            );
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
