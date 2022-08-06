<!-- dynamic load more button-->
@if(isset($page['visibility_show_load_more']) && $page['visibility_show_load_more'])
<div class="autoload loadmore-button-container" id="team_see_more_button">
    <a data-url="{{ $page['url'] ?? '' }}" data-loading-target="{{ $page['loading_target'] ?? '' }}"
        href="javascript:void(0)" class="btn btn-rounded btn-secondary js-ajax-ux-request" id="load-more-button">{{ cleanLang(__('lang.show_more')) }}</a>
</div>
@endif
<!-- /#dynamic load more button-->