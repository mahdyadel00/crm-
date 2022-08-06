<!--each file attachment-->
<div class="col-sm-12 col-md-6 col-lg-4">
    <div class="file-attachment">
        @if($attachment->attachment_type == 'image')
        <div class="">
            <a class="fancybox preview-image-thumb"
                href="storage/files/{{ $attachment->attachment_directory }}/{{ $attachment->attachment_filename  }}"
                title="{{ str_limit($attachment->attachment_filename, 60) }}"
                alt="{{ str_limit($attachment->attachment_filename, 60) }}">
                <img class="x-image" src="{{ url('storage/files/' . $attachment->attachment_directory .'/'. $attachment->attachment_thumbname) }}">
            </a>
        </div>
        @else
        <div class="x-image"> {{ $attachment->attachment_extension }}</div>
        @endif
        <div class="x-details">
            <div class="x-name"><span
                    title="{{ $attachment->attachment_filename }}">{{ str_limit($attachment->attachment_filename, 16) }}</span>
            </div>
            <div class="x-date"><strong>{{ $attachment->creator->full_name ?? __('lang.unknown') }}</strong></div>
            <div class="x-actions"><strong><a
                        href="tickets/attachments/download/{{ $attachment->attachment_uniqiueid }}" download>{{ cleanLang(__('lang.download')) }} <span
                            class="x-icons"><i class="ti-download"></i></span></strong></a></div>
        </div>
    </div>
</div>
<!--each file attachment-->