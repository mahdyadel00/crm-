@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!-- action buttons -->
@include('pages.settings.sections.taxrates.misc.list-page-actions')
<!-- action buttons -->

<!--heading-->
@include('pages.settings.sections.taxrates.table.table')
@endsection