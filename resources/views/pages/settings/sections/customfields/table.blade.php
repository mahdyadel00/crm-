@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--heading-->
<form>
    <div class="table-responsive p-b-30 customfields-table">
        <table id="custom-fields" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10"">
            <thead>
            <th>{{ cleanLang(__('lang.form_field_name')) }}</th>
            <th class=" w-px-80 actions_column">{{ cleanLang(__('lang.settings')) }}</th>
            </tr>
            </thead>
            <tbody id="customfields-td-container">
                @include('pages.settings.sections.customfields.ajax')
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <!--load more button-->
                        @include('misc.load-more-button')
                        <!--load more button-->
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div>
        <!--settings documentation help-->
        <a href="" target="_blank" class="btn btn-sm btn-info  help-documentation"><i class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>

    </div>
    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="custom-fields-save-button"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request"
            data-url="{{ $payload['save_button_url'] }}" data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection