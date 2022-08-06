@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!-- action buttons -->
@include('pages.settings.sections.milestones.misc.list-page-actions')
<!-- action buttons -->

<!--heading-->
@include('pages.settings.sections.milestones.table.table')

<!--section js resource-->
@endsection