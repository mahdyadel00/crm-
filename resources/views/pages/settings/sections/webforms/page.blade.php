@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!-- action buttons -->
@include('pages.settings.sections.roles.misc.list-page-actions')
<!-- action buttons -->

<!--heading-->
@include('pages.settings.sections.webforms.table.table')
@endsection