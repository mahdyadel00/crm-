<?php

/** -------------------------------------------------------------------------------------------------
 * RECURRING INVOICES - DAILY CYCLE
 * Get invoices that are due to be renewed today:
 *         - get
 * This cronjob is envoked by by the task scheduler which is in 'application/app/Console/Kernel.php'
 * @package    Grow CRM
 * @author     NextLoop
 *---------------------------------------------------------------------------------------------------*/

namespace App\Cronjobs;
use App\Repositories\TaskRepository;
use Log;

class RecurringTasksCron {

    public function __invoke(
        TaskRepository $taskrepo
    ) {

        //[MT] - tenants only
        if (env('MT_TPYE')) {
            if (\Spatie\Multitenancy\Models\Tenant::current() == null) {
                return;
            }
        }

        //todays date
        $today = \Carbon\Carbon::now()->format('Y-m-d');

        //get the tasks that are due to recur today and clone them
        $limit = 10;
        if ($tasks = \App\Models\Task::Where('task_recurring', 'yes')
            ->Where('task_recurring_next', $today)
            ->Where('task_recurring_finished', 'no')->take($limit)->get()) {

            Log::info("found some recurring tasks", ['process' => '[RecurringInvoicesCron]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);

            //loop through and clone each task
            foreach ($tasks as $task) {

                //skip tasks that has expired
                if ($task->task_recurring_cycles > 0 && $task->task_recurring_cycles_counter >= $task->task_recurring_cycles) {
                    $task->task_recurring_finished = 'yes';
                    $task->save();
                    continue;
                }

                //get the linked project
                if ($project = \App\Models\Project::Where('project_id', $task->task_projectid)->first()) {

                    //clone the task
                    $data = [
                        'recurring_cron' => true,
                        'task_title' => $task->task_title,
                        'task_status' => 1,
                        'task_milestoneid' => $task->task_milestoneid,
                        'copy_checklist' => ($task->task_recurring_copy_checklists == 'yes') ? true : false,
                        'copy_files' => ($task->task_recurring_copy_files == 'yes') ? true : false,
                    ];
                    $new_task = $taskrepo->cloneTask($task, $project, $data);

                    //updat new task
                    $new_task->task_recurring_child = 'yes';
                    $new_task->task_recurring_parent_id = $task->task_id;
                    $new_task->save();

                    //update next due date for recurring
                    switch ($task->task_recurring_period) {

                    case 'day':
                        $task->task_recurring_next = \Carbon\Carbon::now()->addDays($task->task_recurring_duration)->format('Y-m-d');
                        $task->save();
                        break;

                    case 'week':
                        $task->task_recurring_next = \Carbon\Carbon::now()->addWeeks($task->task_recurring_duration)->format('Y-m-d');
                        $task->save();
                        break;

                    case 'month':
                        $task->task_recurring_next = \Carbon\Carbon::now()->addMonths($task->task_recurring_duration)->format('Y-m-d');
                        $task->save();
                        break;

                    case 'year':
                        $task->task_recurring_next = \Carbon\Carbon::now()->addYears($task->task_recurring_duration)->format('Y-m-d');
                        $task->save();
                        break;
                    }

                    //increare task counter
                    $task->task_recurring_cycles_counter += 1;
                    $task->task_recurring_last = now();
                    $task->task_recurring_finished = 'no';
                    $task->save();

                } else {
                    //stop this recurring - its not valid
                    $task->task_recurring = 'no';
                    $task->save();
                }
            }

        }

        //reset last cron run data
        \App\Models\Settings::where('settings_id', 1)
            ->update([
                'settings_cronjob_has_run' => 'yes',
                'settings_cronjob_last_run' => now(),
            ]);
    }
}