<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //update
        $html = view('pages/leads/components/table/ajax', compact('leads'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#lead_" . $leads->first()->lead_id,
            'action' => 'replace-with',
            'value' => $html);

        //assigned update
        if (isset($type) && $type == 'update-assigned') {
            //new list of assigned users
            $html = view('pages/lead/components/assigned', compact('assigned'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#lead-assigned-container",
                'action' => 'replace',
                'value' => $html);
            $jsondata['dom_classes'][] = [
                'selector' => '#lead-assigned-container',
                'action' => 'remove',
                'value' => 'loading-placeholder',
            ];

        }

        //update name
        if (isset($type) && $type == 'update-name') {
            $jsondata['dom_html'][] = array(
                'selector' => "#card-lead-firstname-containter",
                'action' => 'replace',
                'value' => $firstname);
            $jsondata['dom_html'][] = array(
                'selector' => "#card-lead-lastname-containter",
                'action' => 'replace',
                'value' => $firstlast);
            $jsondata['dom_classes'][] = [
                'selector' => '#card-lead-element-container-name',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //[other] update value
        if (isset($type) && $type == 'update-value') {
            $jsondata['dom_html'][] = [
                'selector' => "#card-lead-value",
                'action' => 'replace',
                'value' => ($amount > 0) ? runtimeMoneyFormat($amount) : '---',
            ];
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-lead-value',
                'action' => 'remove',
                'value' => 'loading');
            $jsondata['dom_attributes'][] = [
                'selector' => "#card-lead-value",
                'attr' => 'data-value',
                'value' => $amount,
            ];
        }

        //[other] update category
        if (isset($type) && $type == 'update-category') {
            $jsondata['dom_html'][] = [
                'selector' => "#card-lead-category-text",
                'action' => 'replace',
                'value' => $new_lead_category,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#card-lead-category-text',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //[other] update phone
        if (isset($type) && $type == 'update-phone') {
            $jsondata['dom_html'][] = [
                'selector' => "#card-lead-phone",
                'action' => 'replace',
                'value' => ($phone == '') ? '---' : $phone,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#card-lead-phone',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //[other] update email
        if (isset($type) && $type == 'update-email') {
            $jsondata['dom_html'][] = [
                'selector' => "#card-lead-email",
                'action' => 'replace',
                'value' => ($email == '') ? '---' : $email,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#card-lead-email',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //[other] update email
        if (isset($type) && $type == 'update-source') {
            $jsondata['dom_html'][] = [
                'selector' => "#card-lead-source-text",
                'action' => 'replace',
                'value' => ($source == '') ? '---' : $source,
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#card-lead-source-text',
                'action' => 'remove',
                'value' => 'loading',
            ];
        }

        //update error
        if (isset($error) && isset($message)) {
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => $message,
            ];
        }

        //kanbad
        $board['leads'] = $leads;
        $html = view('pages/leads/components/kanban/card', compact('board'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#card_lead_" . $leads->first()->lead_id,
            'action' => 'replace-with',
            'value' => $html);

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#actionsModal', 'action' => 'close-modal');

        //response
        return response()->json($jsondata);
    }

}
