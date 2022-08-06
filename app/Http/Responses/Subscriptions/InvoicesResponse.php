<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [create] process for the subscription
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Subscriptions;
use Illuminate\Contracts\Support\Responsable;

class InvoicesResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for subscription members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //load more button - change the page number and determine buttons visibility
        if ($invoices->currentPage() < $invoices->lastPage()) {
            $next_page = $invoices->currentPage() + 1;
            $url = url("/subscriptions/$subscription_id/invoices?page=$next_page");
            $jsondata['dom_attributes'][] = array(
                'selector' => '#load-more-button',
                'attr' => 'data-url',
                'value' => $url);
        } else {
            $jsondata['dom_visibility'][] = array('selector' => '#subscription_load_more_button', 'action' => 'hide');
        }

        //the ajax page
        $html = view('pages/subscription/ajax', compact('invoices'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#subscription-payments',
            'action' => 'append',
            'value' => $html,
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
