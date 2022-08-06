@extends('layout.wrapper') @section('content')
<!-- main content -->
<div class="container-fluid" id="embed-content-container">

    <!-- page content -->
    <form class="input-form" action="/kb/search" method="get">
        <div class="row p-t-10" id="knowledgebase-search-field">
            <div class="col-lg-12">
                <h2 class="text-info text-center">{{ cleanLang(__('lang.knowledgebase')) }}</h2>
                <div class="text-center p-t-5 p-b-40 m-b-30">
                    <h5 class="display-inline-block">{{ cleanLang(__('lang.get_help_from_knowledgebase')) }}</h5> <h5 class="display-inline-block"><a href="/tickets/create">You can also open a support ticket</a></h5>
                </div>
                <div class="input-group hidden">
                    <input type="text" class="form-control" name="search_query" placeholder="{{ cleanLang(__('lang.search')) }}">
                    <span class="input-group-btn">
                        <button class="btn btn-danger" type="submit">{{ cleanLang(__('lang.search')) }}</button>
                    </span>
                </div>
            </div>
        </div>
    </form>

    <!-- page content -->
    <div class="row" id="categories-container">
        @include('pages.kbcategories.components.list.ajax')
    </div>

</div>
<!--main content -->
@endsection