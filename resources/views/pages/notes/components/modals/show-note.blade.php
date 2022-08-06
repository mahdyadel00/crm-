<div class="row">
    <div class="col-lg-12">
            <div class="p-b-10 text-right"><small>{{ runtimeDate($note->note_created) }}</small></div>        
<div class="p-b-30">{!! clean($note->note_description) ?? '---' !!}</div>
    </div>
</div>