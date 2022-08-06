<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index] process for the time billing
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Timebilling;
use Illuminate\Contracts\Support\Responsable;

class IndexResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for bars
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //full payload array
        $payload = $this->payload;

        //flip sorting url for this particular link - only is we clicked sort menu links
        if (request('action') == 'sort') {
            $sort_url = flipSortingUrl(request()->fullUrl(), request('sortorder'));
            $element_id = '#sort_' . request('orderby');
            $jsondata['dom_attributes'][] = array(
                'selector' => $element_id,
                'attr' => 'data-url',
                'value' => $sort_url);
        }

        //render the view and save to json
        $html = view('pages/bill/components/timebilling/tasks/table', compact('tasks', 'project_id', 'billing_rate'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#timebilling-table-wrapper',
            'action' => 'replace',
            'value' => $html);

        if ($count > 0) {
            $jsondata['dom_visibility'][] = [
                'selector' => '#timebillingModalSelectButton',
                'action' => 'show',
            ];
        }

        //rerun this function
        $jsondata['rerun'] = 'updateDomAttributes';

        //rerun this function
        $jsondata['skip_dom_reset'] = true;

        //ajax response
        return response()->json($jsondata);

    }

}
