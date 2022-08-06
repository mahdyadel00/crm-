<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [checklist] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class ChecklistResponse implements Responsable {

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

        // CHECKLIST PROGRESS BAR---
        $html = view('pages/task/components/progressbar', compact('progress'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklist-progress-container',
            'action' => 'replace',
            'value' => $html);

        // CHECKLIST PROGRESS COUNTER---

        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklist-progress',
            'action' => 'replace',
            'value' => $progress['completed']);

        //DELETING A ROW
        if (isset($action) && $action == 'delete') {
            $jsondata['dom_visibility'][] = array(
                'selector' => "#task_checklist_container_$checklistid",
                'action' => 'slideup-slow-remove',
            );
        }

        //response
        return response()->json($jsondata);

    }

}
