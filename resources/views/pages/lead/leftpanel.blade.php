<!--title-->
@include('pages.lead.components.title')


<!--description-->
@include('pages.lead.components.description')


<!--checklist-->
@include('pages.lead.components.checklists')


<!--attachments-->
@include('pages.lead.components.attachments')



<!--comments-->
<div class="card-comments" id="card-comments">
    <div class="x-heading"><i class="mdi mdi-message-text"></i>Comments</div>
    <div class="x-content">
        @include('pages.lead.components.post-comment')
        <!--comments-->
        <div id="card-comments-container">
            <!--dynamic content here-->
        </div>
    </div>
</div>