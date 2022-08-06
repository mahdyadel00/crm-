<div class="card knowledgebase-sidepanel">
    <div class="card-body">

        <!--categories-->
        <div class="x-section">
            <h4>{{ cleanLang(__('lang.categories')) }}</h4>
            <ul>
                @foreach($categories as $category)
                <li><a href="/kb/articles/{{ $category->kbcategory_slug }}"">{{ $category->kbcategory_title }}</a></li>
                @endforeach
            </ul>
        </div>

        <!--related questions-->
        @if(isset($page['vsibility_related_questions']) && $page['vsibility_related_questions'] =='yes')
        <div class="x-section">
                <h4>{{ cleanLang(__('lang.related')) }}</h4>
            <ul>
                @foreach($questions as $question)
                <li>{{ $question->knowledgebase_title }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!--get help-->
        @if(auth()->user()->is_client)
        <div class="x-section">
                            <h4>{{ cleanLang(__('lang.need_more_help')) }}?</h4>
            <div class="x-support">
            <img src="{{ url('/') }}/public/images/get-support.png" /> 
            <a href="/tickets/create" class="btn btn-sm btn-rounded-x btn-danger edit-add-modal-button">{{ cleanLang(__('lang.open_a_support_ticket')) }}</a>
        </div>
        @endif
    </div>

    </div>
</div>