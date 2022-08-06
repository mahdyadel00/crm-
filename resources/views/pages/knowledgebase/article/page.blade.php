<div class="kb-article">
    @if($category->kbcategory_type == 'text')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title card-title-underlined">{{ $knowledgebase->knowledgebase_title }}</h4>
            <p class="card-text">{!! _clean($knowledgebase->knowledgebase_text) !!}</p>
        </div>
    </div>
    @endif

    @if($category->kbcategory_type == 'video')
    <div class="card">
        <div class="card-body">
            <div class="kb-video">
                {!! $knowledgebase->knowledgebase_embed_code !!}
                <h4 class="card-title p-t-10">{{ $knowledgebase->knowledgebase_title }}</h4>
            </div>
        </div>
    </div>
    @endif
</div>