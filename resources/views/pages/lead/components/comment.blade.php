@foreach($comments as $comment)
<div class="display-flex flex-row comment-row" id="card_comment_{{ $comment->comment_id }}">
    <div class="p-2 comment-avatar">
        <img src="{{ getUsersAvatar($comment->avatar_directory, $comment->avatar_filename) }}" class="img-circle"
            alt="{{ $comment->first_name ?? runtimeUnkownUser() }}" width="40">
    </div>
    <div class="comment-text w-100 js-hover-actions">
        <div class="row">
            <div class="col-sm-6 x-name">{{ $comment->first_name ?? runtimeUnkownUser() }}</div>
            <div class="col-sm-6 x-meta text-right">
                <!--meta-->
                <span class="x-date"><small>{{ runtimeDateAgo($comment->comment_created) }}</small></span>
                <!--actions: delete-->
                @if($comment->permission_delete_comment)
                <span class="comment-actions"> | 
                    <a href="javascript:void(0)" class="js-delete-ux-confirm confirm-action-danger text-danger"
                        data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-ajax-type="DELETE"
                        data-parent-container="card_comment_{{ $comment->comment_id }}"
                        data-progress-bar="hidden"
                        data-url="{{ url('/') }}/leads/delete-comment/{{ $comment->comment_id }}">
                        <small>{{ cleanLang(__('lang.delete')) }}</small>
                    </a>
                </span>
                @endif
            </div>
        </div>
        <div class="p-t-4">{!! clean($comment->comment_text) !!}</div>
    </div>
</div>
@endforeach