<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store] process for the kb settings
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Settings\Knowledgebase;
use Illuminate\Contracts\Support\Responsable;

class StoreCategoryResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for sources
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        
        request()->session()->flash('success-notification', __('lang.request_has_been_completed'));

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');       
        
        $jsondata['redirect_url'] = url('app/settings/knowledgebase/default');

        //response
        return response()->json($jsondata);

    }

}
