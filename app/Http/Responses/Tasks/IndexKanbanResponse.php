<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [index kanban] process for the tasks
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Tasks;
use Illuminate\Contracts\Support\Responsable;

class IndexKanbanResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for task members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //was this call made from an embedded page/ajax or directly on task page
        if (request('source') == 'ext' || request('action') == 'search' || request()->ajax()) {

            //tasklate and dom - for additional ajax loading
            switch (request('action')) {

            //from the sorting links
            case 'sort':
                $template = 'pages/tasks/components/kanban/wrapper';
                $dom_container = '#tasks-view-wrapper';
                $dom_action = 'replace-with';
                break;

            //from search box or filter panel
            case 'search':
                $template = 'pages/tasks/components/kanban/wrapper';
                $dom_container = '#tasks-view-wrapper';
                $dom_action = 'replace-with';
                break;

            //tasklate and dom - for ajax initial loading
            default:
                $template = 'pages/tasks/tabswrapper';
                $dom_container = '#embed-content-container';
                $dom_action = 'replace';
                break;
            }

            //load more button - change the page number and determine buttons visibility
            if (request('action') == 'load') {
                foreach ($boards as $board) {
                    $tasks = $board['tasks'];
                    $name = $board['name'];
                    $board_id = $board['id'];
                    //only update the clicked button
                    if (request('source') == $name) {
                        if ($tasks->currentPage() < $tasks->lastPage()) {
                            $url = loadMoreButtonUrl($tasks->currentPage() + 1, request('source'));
                            $jsondata['dom_attributes'][] = array(
                                'selector' => '#load-more-button-' . $board_id,
                                'attr' => 'data-url',
                                'value' => $url);
                            //load more - visible
                            //$jsondata['dom_visibility'][] = array('selector' => '#tasks-loadmore-container-' . $board_id, 'action' => 'show');
                        } else {
                            $jsondata['dom_visibility'][] = array('selector' => '#tasks-loadmore-container-' . $board_id, 'action' => 'hide');
                        }
                    }
                }
            } else {
                foreach ($boards as $board) {
                    $tasks = $board['tasks'];
                    $name = $board['name'];
                    $board_id = $board['id'];
                    if ($tasks->currentPage() < $tasks->lastPage()) {
                        $url = loadMoreButtonUrl($tasks->currentPage() + 1, request('source'));
                        $jsondata['dom_attributes'][] = array(
                            'selector' => '#load-more-button-' . $board_id,
                            'attr' => 'data-url',
                            'value' => $url);
                        //load more - visible
                        //$jsondata['dom_visibility'][] = array('selector' => '#tasks-loadmore-container-' . $board_id, 'action' => 'show');
                    } else {
                        $jsondata['dom_visibility'][] = array('selector' => '#tasks-loadmore-container-' . $board_id, 'action' => 'hide');
                    }
                }
            }

            //flip sorting url for this particular link - only is we clicked sort menu links
            if (request('action') == 'sort') {
                $sort_url = flipSortingUrl(request()->fullUrl(), request('sortorder'));
                $element_id = '#sort_kanban_' . request('orderby');
                $jsondata['dom_attributes'][] = array(
                    'selector' => $element_id,
                    'attr' => 'data-url',
                    'value' => $sort_url);
            }

            //render the view and save to json
            if (request('action') == 'load') {
                //only append to the single board (load more button clicked)
                if (request()->filled('source')) {
                    $name = request('source');
                    $board = $boards[$name];
                    $board_id = $board['id'];
                    $html = view('pages/tasks/components/kanban/card', compact('board'))->render();
                    $jsondata['dom_html'][] = array(
                        'selector' => '#kanban-board-wrapper-' . $board_id,
                        'action' => $dom_action,
                        'value' => $html);
                }
            } else {
                $html = view($template, compact('page', 'boards', 'stats', 'tags', 'milestones', 'statuses'))->render();
                $jsondata['dom_html'][] = array(
                    'selector' => $dom_container,
                    'action' => $dom_action,
                    'value' => $html);
            }

            //move the actions buttons
            if (request('source') == 'ext' && request('action') == '') {
                $jsondata['dom_move_element'][] = array(
                    'element' => '#list-page-actions',
                    'newparent' => '.parent-page-actions',
                    'method' => 'replace',
                    'visibility' => 'show');
                $jsondata['dom_visibility'][] = [
                    'selector' => '#list-page-actions-container',
                    'action' => 'show',
                ];
            }

            //for embedded - change breadcrumb title
            $jsondata['dom_html'][] = [
                'selector' => '.active-bread-crumb',
                'action' => 'replace',
                'value' => strtoupper(__('lang.tasks')),
            ];

            //for embed -change active tabs menu
            $jsondata['dom_classes'][] = [
                'selector' => '.tabs-menu-item',
                'action' => 'remove',
                'value' => 'active',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#tabs-menu-tasks',
                'action' => 'add',
                'value' => 'active',
            ];

            //reload stats widget
            $html = view('misc/list-pages-stats', compact('stats'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => '#list-pages-stats-widget',
                'action' => 'replace-with',
                'value' => $html);
            //stats visibility of reload
            if (auth()->user()->stats_panel_position == 'open') {
                $jsondata['dom_visibility'][] = [
                    'selector' => '#list-pages-stats-widget',
                    'action' => 'show-flex',
                ];
            }

            //filter my tasks button
            if (auth()->user()->pref_filter_own_tasks == 'yes') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#pref_filter_own_tasks',
                    'action' => 'add',
                    'value' => 'active',
                ];
            } else {
                $jsondata['dom_classes'][] = [
                    'selector' => '#pref_filter_own_tasks',
                    'action' => 'remove',
                    'value' => 'active',
                ];
            }


            
            //filter show archvied tasks
            if (auth()->user()->pref_filter_show_archived_tasks == 'yes') {
                $jsondata['dom_classes'][] = [
                    'selector' => '#pref_filter_show_archived_tasks',
                    'action' => 'add',
                    'value' => 'active',
                ];
            } else {
                $jsondata['dom_classes'][] = [
                    'selector' => '#pref_filter_show_archived_tasks',
                    'action' => 'remove',
                    'value' => 'active',
                ];
            }


            //add kanban layout
            $jsondata['dom_classes'][] = [
                'selector' => '#pref_view_tasks_layout',
                'action' => 'add',
                'value' => 'active',
            ];
            $jsondata['dom_classes'][] = [
                'selector' => '#main-body',
                'action' => 'add',
                'value' => 'kanban',
            ];
            $jsondata['dom_visibility'][] = [
                'selector' => '#list_actions_sort_kanban',
                'action' => 'show',
            ];

            // POSTRUN FUNCTIONS------
            $jsondata['postrun_functions'][] = [
                'value' => 'NXTasksKanban',
            ];

            //ajax response
            return response()->json($jsondata);

        } else {
            //standard view
            return view('pages/tasks/wrapper', compact('page', 'boards', 'stats', 'tags', 'milestones', 'statuses'))->render();
        }
    }
}
