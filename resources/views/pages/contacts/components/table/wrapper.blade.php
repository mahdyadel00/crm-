<!--bulk actions-->
@include('pages.contacts.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.contacts.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.contacts.components.misc.filter-contacts')
@endif
<!--filter-->