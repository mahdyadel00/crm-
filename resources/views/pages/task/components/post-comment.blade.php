@if($task->permission_participate)
<!--complete commenting form-->
<div class="post-comment" id="post-card-comment-form">
    <!--placeholder textbox-->
    <div class="x-message-field x-message-field-placeholder m-b-10" id="card-coment-placeholder-input-container"
        data-show-element-container="card-comment-tinmyce-container">
        <textarea class="form-control form-control-sm w-100" rows="1"
            id="card-coment-placeholder-input">{{ cleanLang(__('lang.post_a_comment')) }}...</textarea>
    </div>
    <!--rich text editor-->
    <div class="x-message-field hidden" id="card-comment-tinmyce-container">
        <!--tinymce editor-->
        <textarea class="form-control form-control-sm w-99" rows="2" id="card-comment-tinmyce"
            name="comment_text" id="comment_text"></textarea>
        <!--close button-->
        <div class="x-button p-t-10 p-b-10 text-right">
            <button type="button" class="btn btn-default btn-sm" id="card-comment-close-button">
                {{ cleanLang(__('lang.close')) }}
            </button>
            <!--submit button-->
            <button type="button" class="btn btn-danger btn-sm x-submit-button" id="card-comment-post-button"
                data-url="{{ urlResource('/tasks/'.$task->task_id.'/post-comment') }}" data-type="form" data-ajax-type="post"
                data-form-id="post-card-comment-form" data-loading-target="card-coment-placeholder-input-container">
                {{ cleanLang(__('lang.post')) }}
            </button>
        </div>
    </div>
</div>
<!--/#complete commenting form-->
@endif