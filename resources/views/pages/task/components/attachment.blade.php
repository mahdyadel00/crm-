@foreach($attachments as $attachment)
<div class="col-sm-12" id="card_attachment_{{ $attachment->attachment_uniqiueid }}">
    <div class="file-attachment">
        @if($attachment->attachment_type == 'image')
        <!--dynamic inline style-->
        <div class="">
            <a class="fancybox preview-image-thumb"
                href="storage/files/{{ $attachment->attachment_directory }}/{{ $attachment->attachment_filename  }}"
                title="{{ str_limit($attachment->attachment_filename, 60) }}"
                alt="{{ str_limit($attachment->attachment_filename, 60) }}">
                <img class="x-image" src="{{ url('storage/files/' . $attachment->attachment_directory .'/'. $attachment->attachment_thumbname) }}">
            </a>
        </div>
        @else
        <div class="x-image"> 
            <a class="preview-image-thumb" href="tasks/download-attachment/{{ $attachment->attachment_uniqiueid }}" download>
            {{ $attachment->attachment_extension }}
            </a>
        </div>
        @endif
        <div class="x-details">
            <div><span class="x-meta">{{ $attachment->first_name ?? runtimeUnkownUser() }}</span>
                [{{ runtimeDateAgo($attachment->attachment_created) }}]</div>
            <div class="x-name"><span
                    title="{{ $attachment->attachment_filename }}">{{ str_limit($attachment->attachment_filename, 60) }}</span>
            </div>
            <div class="x-actions"><strong>
                    <!--action: download-->
                    <a href="tasks/download-attachment/{{ $attachment->attachment_uniqiueid }}" download>{{ cleanLang(__('lang.download')) }} <span
                            class="x-icons"><i class="ti-download"></i></span></strong></a>
                <!--action: delete-->
                @if($attachment->permission_delete_attachment)
                <span> |
                    <a href="javascript:void(0)" class="text-danger js-delete-ux-confirm confirm-action-danger"
                        data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}" data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                        data-ajax-type="DELETE"
                        data-parent-container="card_attachment_{{ $attachment->attachment_uniqiueid }}"
                        data-progress-bar="hidden"
                        data-url="{{ urlResource('/tasks/delete-attachment/'.$attachment->attachment_uniqiueid) }}">{{ cleanLang(__('lang.delete')) }}</a></span>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach