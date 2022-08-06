@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!-- action buttons -->
@include('pages.settings.sections.sources.misc.list-page-actions')
<!-- action buttons -->

<!--heading-->
@include('pages.settings.sections.sources.table.table')
@endsection