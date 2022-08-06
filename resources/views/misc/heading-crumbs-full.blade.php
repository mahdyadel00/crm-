<!--full width breadcrum heading/title-->
<div class="col-lg-12 {{ $page['crumbs_special_class'] ?? '' }}" id="breadcrumbs">
    <h3 class="text-themecolor">{{ $page['heading'] ?? '' }}</h3>
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