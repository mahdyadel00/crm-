<!--notes: expects an object named $tags. set this in calling template-->
<a tabindex="0" data-trigger="focus" data-toggle="popover" data-offset="0 4" data-html="true" data-placement="top" data-content="
             <div class='title'>All Tags</div>
             <div class='text-center'>
             @foreach($tags as $tag)
             <span class='tag'>{{ $tag->tag_title }}</span>
             @endforeach
             </div>">
    <span class="btn btn-outline-secondary btn-more">
        <i class="ti-more"></i>
    </span>
</a>