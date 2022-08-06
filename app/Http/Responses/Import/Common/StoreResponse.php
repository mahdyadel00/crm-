<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the foo
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Import\Common;
use Illuminate\Contracts\Support\Responsable;

class StoreResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for foo members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //(1) we have zero errors and some rows were imported
        if ($count_passed > 0 && $error_count == 0) {
            $html = view('pages/import/common/passed', compact('count_passed', 'error_count', 'error_ref', 'type'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#importing-modal-container',
                'action' => 'replace',
                'value' => $html);
        }

        //(2) some rows passed and some failed
        if ($count_passed > 0 && $error_count > 0) {
            $html = view('pages/import/common/partial', compact('count_passed', 'error_count', 'error_ref', 'type'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#importing-modal-container',
                'action' => 'replace',
                'value' => $html);
        }

        //(3) nothing was imported and no error - perhaps a blank xls file?
        if ($count_passed == 0 && $error_count == 0) {
            $html = view('pages/import/common/nothing', compact('count_passed', 'error_count', 'error_ref', 'type'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#importing-modal-container',
                'action' => 'replace',
                'value' => $html);
        }

        //(4) all rows failed
        if ($count_passed == 0 && $error_count > 0) {
            $html = view('pages/import/common/failed', compact('count_passed', 'error_count', 'error_ref', 'type'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#importing-modal-container',
                'action' => 'replace',
                'value' => $html);
        }

        //hide footer
        $jsondata['dom_visibility'][] = [
            'selector' => '#commonModalFooter',
            'action' => 'hide',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
