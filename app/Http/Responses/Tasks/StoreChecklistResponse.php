<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [store checklist] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class StoreChecklistResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for checklists
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

        //reset text editor
        $jsondata['dom_val'][] = array(
            'selector' => '.checklist_text',
            'value' => '',
        );

        //prepend content on top of list
        $html = view('pages/task/components/checklist', compact('checklists'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklists-container',
            'action' => 'append',
            'value' => $html);

        //show add new item button
        $jsondata['dom_visibility'][] = [
            'selector' => "#card-checklist-add-new",
            'action' => 'show',
        ];

        //hide checklist form
        $jsondata['dom_visibility'][] = [
            'selector' => "#element-checklist-text",
            'action' => 'hide',
        ];

        //enable the submit button
        $jsondata['dom_property'][] = array(
            'selector' => '#checklist-submit-button',
            'prop' => 'disabled',
            'value' => false);
        $jsondata['dom_classes'][] = array(
            'selector' => '#checklist-submit-button',
            'action' => 'remove',
            'value' => 'button-loading-annimation');

        // CHECKLIST PROGRESS---
        $html = view('pages/task/components/progressbar', compact('progress'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklist-progress-container',
            'action' => 'replace',
            'value' => $html);

        $jsondata['dom_html'][] = array(
            'selector' => '#card-checklist-progress',
            'action' => 'replace',
            'value' => $progress['completed']);

        //update whole kanban card
        $board['tasks'] = $tasks;
        $html = view('pages/tasks/components/kanban/card', compact('board'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#card_task_" . $tasks->first()->task_id,
            'action' => 'replace-with',
            'value' => $html);

        //response
        return response()->json($jsondata);

    }

}
