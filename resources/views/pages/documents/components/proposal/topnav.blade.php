<div class="row">
    <div class="col-lg-12">
        <ul data-modular-id="proposal_tabs_menu" class="nav nav-tabs proposal-tab proposal-top-nav list-pages-crumbs"
            role="tablist">
            <!--prview-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item" href="/proposals/{{ $document->doc_id }}" role="tab"
                    id="tabs-menu-overview">@lang('lang.preview')</a>
            </li>

            <!--edit-->
            <li class="nav-item">
                <a class="nav-link tabs-menu-item" href="/proposals/{{ $document->doc_id }}/edit" role="tab"
                    id="tabs-menu-overview">@lang('lang.edit_proposal')</a>
            </li>
        </ul>
    </div>
</div>