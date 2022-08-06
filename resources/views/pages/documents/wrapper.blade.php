@if(config('visibility.viewing') == 'public')
@include('pages.documents.wrapper-public')
@else
@include('pages.documents.wrapper-auth')
@endif