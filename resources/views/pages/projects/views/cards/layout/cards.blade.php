<div class="count-{{ @count($projects) }}" id="projects-view-wrapper">
    @if (@count($projects) > 0)
    <div class="cards-grid-container row" id="projects-cards-container">
        @include('pages.projects.views.cards.layout.ajax')
    </div>
    <td colspan="20">
        <!--load more button-->
        @include('misc.load-more-button')
        <!--/load more button-->
    </td>
    @endif @if (@count($projects) == 0)
    <!--nothing found-->
    @include('notifications.no-results-found')
    <!--nothing found-->
    @endif
</div>