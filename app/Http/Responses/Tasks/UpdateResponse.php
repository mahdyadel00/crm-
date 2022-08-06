<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for tasks
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

        //replace the row of this record
        $html = view('pages/tasks/components/table/ajax', compact('tasks'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#task_" . $tasks->first()->task_id,
            'action' => 'replace-with',
            'value' => $html);

        //refresh stats
        if (isset($stats)) {
            $html = view('misc/list-pages-stats-content', compact('stats'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#list-pages-stats-widget',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //assigned update
        if (isset($type) && $type == 'update-assigned') {
            //new list of assigned users
            $html = view('pages/task/components/assigned', compact('task', 'assigned'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#task-assigned-container",
                'action' => 'replace',
                'value' => $html);
            //update timer section
            $html = view('pages/task/components/timer', compact('task'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#task-timer-container",
                'action' => 'replace',
                'value' => $html);
            //remove loading annimation
            $jsondata['dom_classes'][] = [
                'selector' => '#task-assigned-container',
                'action' => 'remove',
                'value' => 'loading-placeholder',
            ];
        }

        //update priority
        if (isset($type) && $type == 'update-priority') {
            //update display text
            $jsondata['dom_html'][] = [
                'selector' => '#card-task-priority-text',
                'action' => 'replace',
                'value' => $display_priority,
            ];

            //remove loading
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-task-priority-text',
                'action' => 'remove',
                'value' => 'loading');
        }

        //update priority
        if (isset($type) && $type == 'update-vivibility') {
            //update display text
            $jsondata['dom_html'][] = [
                'selector' => '#card-task-client-visibility-text',
                'action' => 'replace',
                'value' => $display_text,
            ];

            //remove loading
            $jsondata['dom_classes'][] = array(
                'selector' => '#card-task-client-visibility-text',
                'action' => 'remove',
                'value' => 'loading');
        }

        //update tags
        if (isset($type) && $type == 'update-tags') {
            $html = view('pages.task.components.tags', compact('tags', 'task', 'current_tags'))->render();
            $jsondata['dom_html'][] = [
                'selector' => '#card-tags-container',
                'action' => 'replace',
                'value' => $html,
            ];
        }

        //update error
        if (isset($error) && isset($message)) {
            $jsondata['notification'] = [
                'type' => 'error',
                'value' => $message,
            ];
        }

        //update kanban card completely
        $board['tasks'] = $tasks;
        $html = view('pages/tasks/components/kanban/card', compact('board'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#card_task_" . $tasks->first()->task_id,
            'action' => 'replace-with',
            'value' => $html);

        //updating recurring settings
        if (isset($type) && $type == 'update-recurring') {
            if (request('source') == 'modal') {
                $html = view('pages/task/components/recurring', compact('task'))->render();
                $jsondata['dom_html'][] = [
                    'selector' => '#card-left-panel',
                    'action' => 'replace',
                    'value' => $html,
                ];
                //show recurring icon
                $jsondata['dom_visibility'][] = [
                    'selector' => '#task-modal-menu-recurring-icon',
                    'action' => ($action == 'update') ? 'show' : 'hide',
                ];
                //ajax response
                return response()->json($jsondata);
            } else {
                $close_modal = true;
            }
        }

        //close modal
        if (isset($close_modal) && $close_modal) {
            $jsondata['dom_visibility'][] = [
                'selector' => '#commonModal', 'action' => 'close-modal',
            ];
        }

        //status update - move card

        //response
        return response()->json($jsondata);

    }

}
