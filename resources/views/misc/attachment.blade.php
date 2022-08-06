<!--each file attachment-->
<div class="col-md-12 col-lg-6">
    <div class="file-attachment">
        @if($attachment->attachment_type == 'image')
        <!--dynamic inline style x-->
        <div class="x-image">
            <img src="{{ url('storage/files/' . $attachment->attachment_directory .'/'. $attachment->attachment_thumbname }}">
        </div>
        @else
        <div class="x-image"> {{ $attachment->attachment_extension }}</div>
        @endif
        <div class="x-details">
            <div class="x-name">{{ $attachment->attachment_filename }}</div>
            <div class="x-date"><strong>Fred Marks</strong></div>
            <div class="x-actions"><strong><a href="javascript:void(0)">Download <span class="x-icons"><i class="ti-download"></i></span></strong></a></div>
            <!--delete checkbox-->
            <div class="x-delete hidden">
                <label class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="attachment[{{ $attachment->attachment_id }}]">
                    <span class="custom-control-indicator"></span>
                    <span class="custom-control-description"></span>
                </label>
            </div>
        </div>
    </div>
</div>