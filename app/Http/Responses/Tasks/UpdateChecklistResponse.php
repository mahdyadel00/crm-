<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update checklist] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class UpdateChecklistResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * remove the item from the view
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //prepend content on top of list
        $html = view('pages/task/components/checklist', compact('checklists'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#task_checklist_container_" . $checklist->checklist_id,
            'action' => 'replace-with',
            'value' => $html);

        //hide text editor
        $jsondata['dom_visibility'][] = [
            'selector' => '#element-checklist-text',
            'action' => 'hide',
        ];
        $jsondata['dom_visibility'][] = [
            'selector' => '.copied-checklist-text',
            'action' => 'hide-remove',
        ];

        //show item
        $jsondata['dom_visibility'][] = [
            'selector' => "#task_checklist_container_" . $checklist->checklist_id,
            'action' => 'show',
        ];

        //show add new item button
        $jsondata['dom_visibility'][] = [
            'selector' => "#card-checklist-add-new",
            'action' => 'show',
        ];

        //response
        return response()->json($jsondata);

    }

}
