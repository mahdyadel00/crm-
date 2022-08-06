<div class="row">
    <div class="col-lg-12">

        <!--title-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.title')) }}*</label>
            <div class="col-sm-12">
                <input type="text" class="form-control form-control-sm" autocomplete="off" id="note_title"
                    name="note_title" value="{{ $note->note_title ?? '' }}">
            </div>
        </div>

        <!--description-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.description')) }}*</label>
            <div class="col-sm-12">
                <textarea id="note_description" name="note_description"
                    class="tinymce-textarea hidden">{{ $note->note_description ?? '' }}</textarea>
            </div>
        </div>

        <!--tags-->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
            <div class="col-sm-12">
                <select name="tags" id="tags"
                    class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    <!--array of selected tags-->
                    @if(isset($page['section']) && $page['section'] == 'edit')
                    @foreach($note->tags as $tag)
                    @php $selected_tags[] = $tag->tag_title ; @endphp
                    @endforeach
                    @endif
                    <!--/#array of selected tags-->
                    @foreach($tags as $tag)
                    <option value="{{ $tag->tag_title }}"
                        {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>{{ $tag->tag_title }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--/#tags-->


        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>

        <!--info-->
        @if(request('noteresource_type') == 'project' && auth()->user()->is_team)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">{{ cleanLang(__('lang.project_notes_not_visible_to_client')) }}</div>
            </div>
        </div>
        @endif

    </div>
</div>