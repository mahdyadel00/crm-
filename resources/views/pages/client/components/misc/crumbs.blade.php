<!-- Page Title & Bread Crumbs -->
<div class="col-md-12 col-lg-6 align-self-center">
    <h3 class="text-themecolor">{{ $page['heading'] }}</h3>
    <!--crumbs-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item">{{ cleanLang(__('lang.app')) }}</li>
        @if(isset($page['crumbs']))
        @foreach ($page['crumbs'] as $title)
        <li class="breadcrumb-item @if ($loop->last) active @endif">{{ $title ?? '' }}</li>
        @endforeach
        @endif
    </ol>
    <!--crumbs-->
</div>
<!--Page Title & Bread Crumbs -->