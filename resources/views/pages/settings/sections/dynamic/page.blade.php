@extends('pages.settings.wrapper')
@section('settings-page')
<span id="dynamic-settings-content" data-loading-target="embed-content-container"
    data-url="{{ $page['dynamic_url'] ?? '' }}"></span>
@endsection