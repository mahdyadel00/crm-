<div class="row project-details" id="project-details-container">
    <div class="col-sm-12 tinymce-transparent">
        <!--textarea & editor area-->
        <div class="project-description p-0 p-t-40 rich-text-formatting" id="project-description"> {!! $project->project_description !!}
        </div>
        <!--dynamic description field-->
        <input type="hidden" name="description" id="description" value="">

        <!--editable tags-->
        <div class="form-group row hidden m-t-10" id="project-details-edit-tags">
            <label class="col-12 strong">{{ cleanLang(__('lang.tags')) }}</label>
            <div class="col-12">
                <select name="tags" id="tags"
                    class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    <!--array of selected tags-->
                    @foreach($project->tags as $tag)
                    @php $selected_tags[] = $tag->tag_title ; @endphp
                    @endforeach
                    <!--/#array of selected tags-->
                    @foreach($tags as $tag)
                    <option value="{{ $tag->tag_title }}"
                        {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>{{ $tag->tag_title }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--/#editable tags-->
        <!--tags holder-->
        @if(auth()->user()->is_team)
        <div class="p-t-20" id="project-details-tags">
            @foreach($tags as $tag)
            <span class="label label-rounded label-default tag">{{ $tag->tag_title }}</span>
            @endforeach
        </div>
        @endif
        <!--/#tags holder-->

        @if(config('visibility.edit_project_button'))
        <hr>
        </hr>
        <!--buttons: edit-->
        <div id="project-description-edit" class="p-t-20 text-right">
            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                id="project-description-button-edit">{{ cleanLang(__('lang.edit_description')) }}</button>
        </div>

        <!--button: subit & cancel-->
        <div id="project-description-submit" class="p-t-20 hidden text-right">
            <button type="button" class="btn waves-effect waves-light btn-xs btn-default"
                id="project-description-button-cancel">{{ cleanLang(__('lang.cancel')) }}</button>
            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" data-type="form"
                data-form-id="project-details-container" data-ajax-type="post"
                data-url="{{ url('projects/'.$project->project_id .'/project-details') }}"
                id="project-description-button-save">{{ cleanLang(__('lang.save')) }}</button>
        </div>
        @endif

    </div>
</div>