@foreach($comments as $comment)
<!-- each comment -->
<div class="display-flex flex-row comment-row" id="comment_{{ $comment->comment_id }}">
    <div class="p-2">
        <img src="{{ getUsersAvatar($comment->avatar_directory, $comment->avatar_filename)  }}"
            class="img-circle" alt="user" width="40">
    </div>
    <div class="comment-text w-100 js-hover-actions">
        <div class="row">
            <div class="col-sm-6 x-name">{{ $comment->first_name ?? runtimeUnkownUser() }}</div>
            <div class="col-sm-6 x-meta text-right">
                <!--actions-->
                @if($comment->permission_delete_comment)
                <span class="comment-actions js-hover-actions-target hidden">
                    <a href="javascript:void(0)" class="btn-outline-danger confirm-action-danger"
                        data-confirm-title="{{ cleanLang(__('lang.delete_comment')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-ajax-type="DELETE" data-url="{{ url('/comments/'.$comment->comment_id) }}">
                        <i class="sl-icon-trash"></i>
                    </a>
                </span>
                @endif
                <!--actions-->
                <span class="text-muted x-date"><small>{{ runtimeDateAgo($comment->comment_created) }}</small></span>
            </div>
        </div>
        <div>{!! _clean($comment->comment_text) !!}</div>
    </div>
</div>
<!--each comment -->
@endforeach