<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [categorry] process for the items
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Items;
use Illuminate\Contracts\Support\Responsable;

class ChangeCategoryUpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for items
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //generate a new for for each record
        foreach ($allrows as $items) {
            //item id
            $id = $items->first()->item_id;
            //render html
            $html = view('pages/items/components/table/ajax', compact('items'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#item_$id",
                'action' => 'replace-with',
                'value' => $html);
            //check the box again (only for bulk actions)
            if (request('type') == 'bulk') {
                $jsondata['dom_property'][] = [
                    'selector' => '#listcheckbox-items-' . $id,
                    'prop' => 'checked',
                    'value' => true,
                ];
            }
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
