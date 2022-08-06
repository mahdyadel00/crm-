<!--bulk actions-->
@include('pages.invoices.components.actions.checkbox-actions')

<!--main table view-->
@include('pages.invoices.components.table.table')

<!--filter-->
@if(auth()->user()->is_team)
@include('pages.invoices.components.misc.filter-invoices')
@endif
<!--filter-->