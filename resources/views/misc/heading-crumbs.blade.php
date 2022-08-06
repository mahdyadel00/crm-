<!--CRUMBS CONTAINER (LEFT)-->
<div class="col-md-12 {{ runtimeCrumbsColumnSize($page['crumbs_col_size'] ?? '') }} align-self-center {{ $page['crumbs_special_class'] ?? '' }}" id="breadcrumbs">
    <h3 class="text-themecolor">{{ $page['heading'] }}</h3>
    <!--crumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{ cleanLang(__('lang.app')) }}</li>
        @if(isset($page['crumbs']))
        @foreach ($page['crumbs'] as $title) 
        <li class="breadcrumb-item @if ($loop->last) active active-bread-crumb @endif">{{ $title ?? '' }}</li>
        @endforeach
        @endif
    </ol>
    <!--crumbs-->
</div>

<!--include various checkbox actions-->

@if(isset($page['page']) && $page['page'] == 'files')
@include('pages.files.components.actions.checkbox-actions')
@endif

@if(isset($page['page']) && $page['page'] == 'notes')
@include('pages.notes.components.actions.checkbox-actions')
@endif