<div class="card-checklist" id="card-checklist">
    <div class="x-heading clearfix">
        <span class="pull-left"><i class="mdi mdi-checkbox-marked"></i>{{ cleanLang(__('lang.checklist')) }}</span>
        <span class="pull-right p-t-5" id="card-checklist-progress">{{ $progress['completed'] }}</span>
    </div>
    <div class="progress" id="card-checklist-progress-container">
            @include('pages.task.components.progressbar')
    </div>
    <div class="x-content" id="card-checklists-container">
        <!--dynamic content here-->
    </div>
    @if($task->permission_edit_task)
    <div class="x-action">
        <a href="javascript:void(0)" class="js-card-checklist-toggle" id="card-checklist-add-new"
            data-action-url="{{ urlResource('/tasks/'.$task->task_id.'/add-checklist') }}" data-toggle="new">{{ cleanLang(__('lang.add_new_item')) }}</a>
    </div>
    @endif
</div>