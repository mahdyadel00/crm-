<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the leads
 * controller
 * 
 * [IMPORTANT] All Left Panel code must be reproduced in the file ContentResponse.php
 * 
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class ShowResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view
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

        // RIGHT PANEL---
        $html = view('pages/lead/rightpanel', compact('page', 'lead', 'assigned', 'sources', 'statuses', 'tags', 'categories', 'payload', 'reminder', 'tags', 'current_tags'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card--leads-right-panel',
            'action' => 'replace',
            'value' => $html);

        //  LEFT PANEL - MAIN (changes must be reproduced in contentResponse)
        $html = view('pages/lead/leftpanel', compact('page', 'lead', 'progress'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-leads-left-panel',
            'action' => 'replace',
            'value' => $html);

        //  LEFT PANEL - COMMENTS (changes must be reproduced in contentResponse)
        $html = view('pages/lead/components/comment', compact('comments'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-comments-container',
            'action' => 'replace',
            'value' => $html);

        //  LEFT PANEL - ATTACHMENTS (changes must be reproduced in contentResponse)
        $html = view('pages/lead/components/attachment', compact('attachments'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-attachments-container',
            'action' => 'replace',
            'value' => $html);

        //  LEFT PANEL - CHECKLISTS (changes must be reproduced in contentResponse)
        $html = view('pages/lead/components/checklist', compact('checklists', 'progress'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklists-container',
            'action' => 'replace',
            'value' => $html);

        //  LEFT PANEL - PROGRESS (changes must be reproduced in contentResponse)
        $html = view('pages/lead/components/progressbar', compact('progress'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklist-progress-container',
            'action' => 'replace',
            'value' => $html);

        // CONVERT FOOTER---
        $html = view('pages/lead/components/footer', compact('lead'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#leadConvertToCustomerFooter',
            'action' => 'replace',
            'value' => $html);

        //HIDE NOTIFICATION ICONS ON CARDS
        $jsondata['dom_visibility'][] = [
            'selector' => "#card_notification_attachment_$id",
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => "#card_notification_comment_$id",
            'action' => 'hide',
        ];

        // SHOW MODAL------
        $jsondata['dom_classes'][] = [
            'selector' => '#cardModalContent',
            'action' => 'remove',
            'value' => 'hidden',
        ];

        //update browser url
        $jsondata['dom_browser_url'] = [
            'title' => __('lang.lead') . ' - ' . $lead->lead_title,
            'url' => url("/leads/v/" . $lead->lead_id . "/" . str_slug($lead->lead_title)),
        ];

        //show tabs menu
        $html = view('pages/lead/content/tabmenu', compact('lead'))->render();
        $jsondata['dom_html'][] = [
            'selector' => '#cardModalTabMenu',
            'action' => 'replace',
            'value' => $html,
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLeadConvert',
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXLeadAttachFiles',
        ];

        // POSTRUN FUNCTIONS------
        $jsondata['postrun_functions'][] = [
            'value' => 'NXBootCards',
        ];

        //ajax response
        return response()->json($jsondata);

    }

}
