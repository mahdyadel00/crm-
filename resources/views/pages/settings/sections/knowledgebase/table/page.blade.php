@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!-- action buttons -->
@include('pages.settings.sections.knowledgebase.misc.list-page-actions')
<!-- action buttons -->

<!--heading-->
@include('pages.settings.sections.knowledgebase.table.table')

@endsection