<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for cloning projects
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

/** --------------------------------------------------------------------------
 * [Clone Invoice Repository]
 * Clone an project. The new project is set to 'draft status' by default
 * It can be published as needed
 * @source Nextloop
 *--------------------------------------------------------------------------*/
namespace App\Repositories;

use App\Repositories\CloneEstimateRepository;
use App\Repositories\CloneInvoiceRepository;
use App\Repositories\MilestoneCategoryRepository;
use App\Repositories\MilestoneRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Log;

class CloneProjectRepository {

    //vars
    protected $projectrepo;
    protected $tagrepo;
    protected $milestonerepo;
    protected $milestonecategories;
    protected $cloneinvoicerepo;
    protected $cloneestimaterepo;
    protected $data;

    /**
     * Inject dependecies
     */
    public function __construct(
        ProjectRepository $projectrepo,
        TagRepository $tagrepo,
        MilestoneRepository $milestonerepo,
        MilestoneCategoryRepository $milestonecategories,
        CloneInvoiceRepository $cloneinvoicerepo,
        CloneEstimateRepository $cloneestimaterepo
    ) {
        $this->projectrepo = $projectrepo;
        $this->tagrepo = $tagrepo;
        $this->milestonerepo = $milestonerepo;
        $this->milestonecategories = $milestonecategories;
        $this->cloneinvoicerepo = $cloneinvoicerepo;
        $this->cloneestimaterepo = $cloneestimaterepo;

    }

    /**
     * Clone an project
     * @param array data array
     *              - project_id
     *              - project_title
     *              - project_date_start
     *              - project_date_due
     *              - tags
     *              - project_categoryid
     *              - copy_milestones
     *              - copy_tasks
     *              - copy_tasks_files
     *              - copy_tasks_checklist
     *              - copy_invoices
     *              - copy_estimates
     *              - copy_files
     *              - return (id|object)
     * @return mixed int|object
     */
    public function clone ($data = []) {

        //save global
        $this->data = $data;

        //info
        Log::info("cloning project started", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);

        //validate information
        if (!$this->validateData($data)) {
            Log::info("cloning project failed", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get clean the object for cloning
        $project = \App\Models\Project::Where('project_id', $data['project_id'])->first();

        //clone main invoice
        $new_project = $project->replicate();

        //update from provided data
        $new_project->project_importid = $data['project_id'];
        $new_project->project_clientid = $data['project_clientid'];
        $new_project->project_title = $data['project_title'];
        $new_project->project_date_start = $data['project_date_start'];
        $new_project->project_date_due = $data['project_date_due'] ?? null;
        $new_project->project_categoryid = $data['project_categoryid'];

        //update using some defaults
        $new_project->project_status = 'not_started ';
        $new_project->project_active_state = 'active';
        $new_project->project_visibility = 'visible';
        $new_project->project_creatorid = auth()->id();

        //save
        $new_project->save();

        //add tags
        $this->tagrepo->add('project', $new_project->project_id);

        //milestones
        $mileston_map = $this->copyMilestones($project, $new_project);

        //tasks
        $this->copyTasks($project, $new_project, $mileston_map);

        //invoice
        $this->copyInvoices($project, $new_project);

        //estimate
        $this->copyEstimates($project, $new_project);

        //files
        $this->copyFiles($project, $new_project);

        //log its finished
        Log::info("cloning project completed", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'new_project_id' => $new_project->project_id]);

        //return invoice id | invoice object
        if (isset($data['return']) && $data['return'] == 'id') {
            return $new_project->project_id;
        } else {
            return $new_project;
        }
    }

    /**
     * Clone an project templates resources
     * @param array data array
     *              - new_project_id
     *              - template_project_id
     *              - copy_milestones
     *              - copy_tasks
     *              - copy_tasks_files
     *              - copy_tasks_checklist
     * @return mixed int|object
     */
    public function cloneTemplate($data = []) {

        //append data
        $data += [
            'copy_milestones' => 'on',
            'copy_tasks' => 'on',
            'copy_files' => 'on',
            'copy_tasks_files' => 'on',
            'copy_tasks_checklist' => 'on',
        ];

        //save global
        $this->data = $data;

        //validate
        //validation
        if (!isset($data['new_project_id']) || !isset($data['template_project_id'])) {
            Log::error("the supplied data is not valid", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //get clean the object for cloning
        if (!$new_project = \App\Models\Project::Where('project_id', $data['new_project_id'])->first()) {
            Log::error("new project could not be loaded", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'template_project_id' => $data['template_project_id']]);
            return false;
        }

        //get clean the object for cloning
        if (!$template = \App\Models\Project::Where('project_id', $data['template_project_id'])->first()) {
            Log::error("template project could not be found", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'template_project_id' => $data['template_project_id']]);
            return false;
        }

        //milestones
        $mileston_map = $this->copyMilestones($template, $new_project);

        //tasks
        $this->copyTasks($template, $new_project, $mileston_map);

        //files
        $this->copyFiles($template, $new_project);

        //tags
        $this->copyTags($template, $new_project);

        //save global
        $this->data = $data;

    }

    /**
     * copy milestones
     * @return bool
     */
    private function copyMilestones($old_project, $new_project) {

        //milestone map
        $mileston_map = [];

        //we are copying
        if ($this->data['copy_milestones'] == 'on') {
            if ($milestones = \App\Models\Milestone::Where('milestone_projectid', $old_project->project_id)->get()) {
                foreach ($milestones as $milestone) {
                    $new_milestone = $milestone->replicate();
                    $new_milestone->milestone_projectid = $new_project->project_id;
                    $new_milestone->save();
                    //milestone map
                    $mileston_map[$milestone->milestone_id] = $new_milestone->milestone_id;
                }
            }
        }

        //use defauls milestones
        if ($this->data['copy_milestones'] != 'on') {
            $position = $this->milestonecategories->addProjectMilestones($new_project);
            $this->milestonerepo->addUncategorised($new_project->project_id, $position);
        }

        //return map
        return $mileston_map;
    }

    /**
     * copy tasks
     * @return bool
     */
    private function copyTasks($old_project, $new_project, $mileston_map) {

        //we are copying
        if ($this->data['copy_tasks'] == 'on' && $this->data['copy_milestones'] == 'on') {

            if ($tasks = \App\Models\Task::Where('task_projectid', $old_project->project_id)->get()) {
                foreach ($tasks as $task) {
                    $new_task = $task->replicate();
                    $new_task->task_created = now();
                    $new_task->task_creatorid = auth()->id();
                    $new_task->task_clientid = $new_project->project_clientid;
                    $new_task->task_projectid = $new_project->project_id;
                    $new_task->task_date_start = now();
                    $new_task->task_date_due = null;
                    $new_task->task_billable_status = 'not_invoiced';
                    $new_task->task_billable_invoiceid = null;
                    $new_task->task_billable_lineitemid = null;
                    $new_task->task_milestoneid = $mileston_map[$task->task_milestoneid]; //get new milestone id of the cloned milestone
                    $new_task->save();

                    //copy check list
                    if ($this->data['copy_tasks_checklist'] == 'on') {
                        if ($checklists = \App\Models\Checklist::Where('checklistresource_type', 'task')->Where('checklistresource_id', $task->task_id)->get()) {
                            foreach ($checklists as $checklist) {
                                $new_checklist = $checklist->replicate();
                                $new_checklist->checklist_created = now();
                                $new_checklist->checklist_creatorid = auth()->id();
                                $new_checklist->checklist_clientid = $new_project->project_clientid;
                                $new_checklist->checklist_status = 'pending';
                                $new_checklist->checklistresource_type = 'task';
                                $new_checklist->checklistresource_id = $new_task->task_id;
                                $new_checklist->save();
                            }
                        }
                    }

                    //copy attachements
                    if ($this->data['copy_tasks_files'] == 'on') {
                        if ($attachments = \App\Models\Attachment::Where('attachmentresource_type', 'task')->Where('attachmentresource_id', $task->task_id)->get()) {
                            foreach ($attachments as $attachment) {
                                //unique key
                                $unique_key = Str::random(50);
                                //directory
                                $directory = Str::random(40);
                                //paths
                                $source = BASE_DIR . "/storage/files/" . $attachment->attachment_directory;
                                $destination = BASE_DIR . "/storage/files/$directory";
                                //validate
                                if (is_dir($source)) {
                                    //copy the database record
                                    $new_attachment = $attachment->replicate();
                                    $new_attachment->attachment_creatorid = auth()->id();
                                    $new_attachment->attachment_created = now();
                                    $new_attachment->attachmentresource_id = $task->task_id;
                                    $new_attachment->attachment_clientid = $new_project->project_clientid;
                                    $new_attachment->attachment_uniqiueid = $directory;
                                    $new_attachment->attachment_directory = $directory;
                                    $new_attachment->attachmentresource_type = 'task';
                                    $new_attachment->attachmentresource_id = $new_task->task_id;
                                    $new_attachment->save();
                                    //copy folder
                                    File::copyDirectory($source, $destination);
                                }
                            }
                        }
                    }
                }
            }
        }

    }

    /**
     * copy invoices
     * @return bool
     */
    private function copyInvoices($old_project, $new_project) {

        //we are copying
        if ($this->data['copy_invoices'] == 'on') {
            if ($invoices = \App\Models\Invoice::Where('bill_projectid', $old_project->project_id)->get()) {
                foreach ($invoices as $invoice) {
                    //clone data
                    $data = [
                        'invoice_id' => $invoice->bill_invoiceid,
                        'client_id' => $new_project->project_clientid,
                        'project_id' => $new_project->project_id,
                        'invoice_date' => now(), //make invoice data today
                        'invoice_due_date' => \Carbon\Carbon::now()->addMonths(1)->format('Y-m-d'), //30 days from now
                        'return' => 'id',
                    ];

                    //clone invoice
                    if (!$this->cloneinvoicerepo->clone($data)) {
                        Log::error("cloning the project's invoice failed - invoice id(" . $invoice->bill_invoiceid . ") failed", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    }
                }
            }
        }

    }

    /**
     * copy estimates
     * @return bool
     */
    private function copyEstimates($old_project, $new_project) {

        //we are copying
        if ($this->data['copy_estimates'] == 'on') {
            if ($estimates = \App\Models\Estimate::Where('bill_projectid', $old_project->project_id)->get()) {
                foreach ($estimates as $estimate) {
                    //clone data
                    $data = [
                        'estimate_id' => $estimate->bill_estimateid,
                        'client_id' => $new_project->project_clientid,
                        'project_id' => $new_project->project_id,
                        'estimate_date' => now(), //make estimate data today
                        'return' => 'id',
                    ];
                    //clone estimate
                    if (!$this->cloneestimaterepo->clone($data)) {
                        Log::error("cloning the project's estimate failed - estimate id(" . $estimate->bill_estimateid . ") failed", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
                    }
                }
            }
        }
    }

    /**
     * copy files
     * @return bool
     */
    private function copyfiles($old_project, $new_project) {

        //we are copying
        if ($this->data['copy_files'] == 'on') {
            if ($files = \App\Models\File::Where('fileresource_type', 'project')->Where('fileresource_id', $old_project->project_id)->get()) {
                foreach ($files as $file) {
                    //unique key
                    $unique_key = Str::random(50);
                    //directory
                    $directory = Str::random(40);
                    //paths
                    $source = BASE_DIR . "/storage/files/" . $file->file_directory;
                    $destination = BASE_DIR . "/storage/files/$directory";

                    //validate
                    if (is_dir($source)) {
                        //copy the database record
                        $new_file = $file->replicate();
                        $new_file->file_creatorid = auth()->id();
                        $new_file->file_created = now();
                        $new_file->fileresource_id = $new_project->project_id;
                        $new_file->file_clientid = $new_project->project_clientid;
                        $new_file->file_uniqueid = $directory;
                        $new_file->file_directory = $directory;
                        $new_file->file_upload_unique_key = $unique_key;
                        $new_file->save();
                        //copy folder
                        File::copyDirectory($source, $destination);
                    }
                }
            }
        }

    }

    /**
     * copy tags
     * @return bool
     */
    private function copyTags($old_project, $new_project) {

        //we are copying
        if ($tags = \App\Models\Tag::Where('tagresource_type', 'project')->Where('tagresource_id', $old_project->project_id)->get()) {
            foreach ($tags as $tag) {
                $new_tag = $tag->replicate();
                $new_tag->tagresource_id = $new_project->project_id;
                $new_tag->save();
            }
        }
    }

    /**
     * validate required data for cloning an project
     * @param array $data information payload
     * @return bool
     */
    private function validateData($data = []) {

        //validation
        if (!isset($data['project_id']) || !isset($data['project_clientid']) || !isset($data['project_title']) || !isset($data['project_date_start'])) {
            Log::error("the supplied data is not valid", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'payload' => $data]);
            return false;
        }

        //project_id
        if (!is_numeric($data['project_id'])) {
            Log::error("the supplied project id is invalid", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $data['invoice_id']]);
            return false;
        }

        //project_clientid
        if (!is_numeric($data['project_clientid'])) {
            Log::error("the supplied client id is invalid", ['process' => '[CloneProjectRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'invoice_id' => $data['invoice_id']]);
            return false;
        }

        return true;
    }

}