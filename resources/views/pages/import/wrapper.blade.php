<!--PASSED SYSTEM REQUIREMENTS-->
@if($server_status == 'passed')
<div class="importing-modal" id="importing-modal-container">


    <!--FIRST STEP-->
    @include('pages.import.common.first-step')


    <!--SECOND STEP-->
    <div class="hidden" id="importing-step-2">


        <div class="import-payload-preview hidden p-r-30" id="import-payload-preview-csv">
            <a><img src="{{ url('public/images/file-icons/icon-csv.svg') }}" alt="CSV FILE" />
            </a>
        </div>

        <div class="import-payload-preview hidden p-r-30" id="import-payload-preview-xls">
            <a><img src="{{ url('public/images/file-icons/icon-xls.svg') }}" alt="XLS FILE" />
            </a>
        </div>

        <div class="import-payload-preview hidden p-r-30" id="import-payload-preview-xlsx">
            <a><img src="{{ url('public/images/file-icons/icon-xlsx.svg') }}" alt="CSV FILE" />
            </a>
        </div>

        <div class="import-payload-preview-text hidden" id="import-payload-preview-text">
            <div class="x-title" id="import-payload-preview-filename">
                <!--dynamic file name-->
            </div>
            <div class="x-meta" id="import-payload-preview-meta">
                <!--dynamic file size-->
            </div>
        </div>

        <!-- main content -->
        @yield('second-step-form')
        <!-- /#main content -->


        <!--payload-->
        <input type="hidden" name="importing-file-name" id="importing-file-name">
        <input type="hidden" name="importing-file-uniqueid" id="importing-file-uniqueid">
    </div>
</div>
@endif


@if($server_status == 'failed')
@include('pages.import.common.requirements')
@endif