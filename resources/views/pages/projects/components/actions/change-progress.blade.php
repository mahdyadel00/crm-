<div id="js-projects-modal-add-edit" data-project-progress=" {{ $project['project_progress'] ?? 0 }}">
    <div class="form-group form-group-checkbox row">
        <label class="col-10 col-form-label text-left">{{ cleanLang(__('lang.set_progress_manually')) }}?</label>
        <div class="col-2 text-left p-t-5">
            <input type="checkbox" id="project_progress_manually" name="project_progress_manually"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($project['project_progress_manually'] ?? '') }}>
            <label for="project_progress_manually"></label>
        </div>
    </div>

    <div class="modal-selector  m-t-20">

        <div class="form-group row p-t-20">
            <div class="col-sm-10 p-l-30">
                <div id="edit_project_progress_bar"></div>
            </div>
            <div class="col-sm-2 text-right">
                <strong>
                    <span id="edit_project_progress_display">20</span>%</strong>
            </div>
        </div>
        <input type="hidden" name="project_progress" value="{{ $project->project_progress ?? '' }}"
            id="project_progress" />
    </div>
    <div class="alert alert-info m-t-30">
        <h5 class="text-info">@lang('lang.update_progress_info')
    </div>


</div>

<script>
    //page section
    var page_section = $("#js-projects-modal-add-edit").attr('data-section');

    var project_progress = $("#js-projects-modal-add-edit").attr('data-project-progress');

    //reset editor
    nxTinyMCEBasic();

    //progress slider
    var progress = document.getElementById('edit_project_progress_bar');
    noUiSlider.create(progress, {
        start: [project_progress],
        connect: true,
        step: 1,
        range: {
            'min': 0,
            'max': 100
        }
    });
    //set display and hidden form field values
    var project_progress_input = document.getElementById('project_progress');
    var project_progress_display = document.getElementById('edit_project_progress_display');
    progress.noUiSlider.on('update', function (values, handle) {
        project_progress_display.innerHTML = values[handle];
        project_progress_input.value = values[handle];
    });
</script>