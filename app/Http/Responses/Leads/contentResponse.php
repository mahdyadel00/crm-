<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [show] process for the leads
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Leads;
use Illuminate\Contracts\Support\Responsable;

class contentResponse implements Responsable {

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

        /** -------------------------------------------------------------------------
         * show main lead tab (home)
         * -------------------------------------------------------------------------*/
        if ($type == 'show-main') {

            // LEFT PANEL - MAIN (code is copied from ShowResponse)
            $html = view('pages/lead/leftpanel', compact('page', 'lead', 'progress'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html);

            // LEFT PANEL - COMMENTS (code is copied from ShowResponse)
            $html = view('pages/lead/components/comment', compact('comments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#card-comments-container',
                'action' => 'replace',
                'value' => $html);

            // LEFT PANEL - ATTACHMENTS (code is copied from ShowResponse)
            $html = view('pages/lead/components/attachment', compact('attachments'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#card-attachments-container',
                'action' => 'replace',
                'value' => $html);

            // LEFT PANEL - CHECKLIST (code is copied from ShowResponse)
            $html = view('pages/lead/components/checklist', compact('checklists', 'progress'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#card-checklists-container',
                'action' => 'replace',
                'value' => $html);

            //  LEFT PANEL - PROGRESS (code is copied from ShowResponse)
            $html = view('pages/lead/components/progressbar', compact('progress'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#card-checklist-progress-container',
                'action' => 'replace',
                'value' => $html);

            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXLeadAttachFiles',
            ];

            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXBootCards',
            ];

        }

        /** -------------------------------------------------------------------------
         * show organisation tab
         * -------------------------------------------------------------------------*/
        if ($type == 'show-organisation') {
            $html = view('pages/lead/content/organisation/show', compact('lead'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * show edit - organisation tab
         * -------------------------------------------------------------------------*/
        if ($type == 'edit-organisation') {
            $html = view('pages/lead/content/organisation/edit', compact('lead'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * show custom fields tab
         * -------------------------------------------------------------------------*/
        if ($type == 'show-custom-fields') {
            $html = view('pages/lead/content/customfields/show', compact('lead', 'fields'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * edit custom fields tab
         * -------------------------------------------------------------------------*/
        if ($type == 'edit-custom-fields') {
            $html = view('pages/lead/content/customfields/edit', compact('lead', 'fields'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * show user notes
         * -------------------------------------------------------------------------*/
        if ($type == 'show-notes') {
            $html = view('pages/lead/content/mynotes/show', compact('lead', 'note', 'has_note'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * show user notes
         * -------------------------------------------------------------------------*/
        if ($type == 'edit-notes' || $type == 'create-notes') {
            $html = view('pages/lead/content/mynotes/edit', compact('lead', 'note'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        /** -------------------------------------------------------------------------
         * show lead logs
         * -------------------------------------------------------------------------*/
        if ($type == 'show-logs') {
            $html = view('pages/lead/content/log/show', compact('logs'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-leads-left-panel',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //ajax response
        return response()->json($jsondata);

    }

}
