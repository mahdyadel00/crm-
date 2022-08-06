<!--main table view-->
@if (@count($knowledgebase) > 0)
<div class="row youtube-gallery">

    <!--each video-->
    @foreach($knowledgebase as $kb)
    <div class="col-sm-12 col-md-6 col-lg-3">
        <div class="card youtube-gallery-item">
            <a href="{{ url('/') }}/kb/article/{{ $kb->knowledgebase_slug }}">
                <img class="card-img-top img-responsive" src="{{ $kb->knowledgebase_embed_thumb }}"
                    alt="Card image cap">
            </a>
            <div class="card-body p-l-10 p-r-10 p-t-14 p-b-0">
                @if(auth()->user()->role->role_knowledgebase >= 2)
                <div class="x-edit-video">
                    <button type="button" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false" class="btn btn-outline-default-light btn-sm x-edit-video-button">
                        <i class="ti-more"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="listTableAction">
                        <!--edit-->
                        @if(config('visibility.action_buttons_edit'))
                        <a class="dropdown-item actions-modal-button js-ajax-ux-request reset-target-modal-form edit-add-modal-button cursor-pointer"
                            data-toggle="modal" data-target="#commonModal"
                            data-url="{{ url('/kb/'.$kb->knowledgebase_id.'/edit?source='.request('source')) }}"
                            data-loading-target="commonModalBody"
                            data-modal-title="{{ cleanLang(__('lang.edit_article')) }}"
                            data-action-url="{{ url('/kb/'.$kb->knowledgebase_id) }}" data-action-method="PUT"
                            data-action-ajax-class="" data-action-ajax-loading-target="knowledgebase-td-container">
                            {{ cleanLang(__('lang.edit')) }}
                        </a>
                        @endif
                        <!--delete-->
                        @if(config('visibility.action_buttons_delete'))
                        <a class="dropdown-item actions-modal-button  confirm-action-danger cursor-pointer"
                            data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                            data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                            data-url="{{ url('/') }}/kb/{{ $kb->knowledgebase_id }}">
                            {{ cleanLang(__('lang.delete')) }}
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                <h6 class="card-title"><a
                        href="{{ url('/') }}/kb/article/{{ $kb->knowledgebase_slug }}">{{ str_limit($kb->knowledgebase_title ??'---', 50) }}</a>
                </h6>
            </div>
        </div>
    </div>
    @endforeach


</div>





@endif

@if (@count($knowledgebase) == 0)
<!--nothing found-->
@include('notifications.no-results-found')
<!--nothing found-->
@endif