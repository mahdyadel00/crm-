<!--title-->
@include('pages.task.components.title')



<!--description-->
@include('pages.task.components.description')


<!--checklist-->
@include('pages.task.components.checklists')



<!--attachments-->
@include('pages.task.components.attachments')



<!--comments-->
@if(config('visibility.tasks_standard_features'))
<div class="card-comments" id="card-comments">
    <div class="x-heading"><i class="mdi mdi-message-text"></i>Comments</div>
    <div class="x-content">
        @include('pages.task.components.post-comment')
        <!--comments-->
        <div id="card-comments-container">
            <!--dynamic content here-->
        </div>
    </div>
</div>
@endif