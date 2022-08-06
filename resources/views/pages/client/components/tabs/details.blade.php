<div class="row client-details" id="client-details-container">
    <div class="col-sm-12 tinymce-transparent">
        <!--textarea & editor area-->
        <div class="client-description p-0 rich-text-formatting" id="client-description"> {!!
            clean($client->client_description) !!}
        </div>
        <!--dynamic description field-->
        <input type="hidden" name="description" id="description" value="">

        <!--editable tags-->
        <div class="form-group row hidden m-t-10" id="client-details-edit-tags">
            <label class="col-12 strong">{{ cleanLang(__('lang.tags')) }}</label>
            <div class="col-12">
                <select name="tags" id="tags"
                    class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    <!--array of selected tags-->
                    @foreach($client->tags as $tag)
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
        <div class="p-t-20" id="client-details-tags">
            @foreach($tags as $tag)
            <span class="label label-rounded label-default tag">{{ $tag->tag_title }}</span>
            @endforeach
        </div>
        @endif
        <!--/#tags holder-->

        @if(config('visibility.edit_client_button'))
        <hr>
        </hr>
        <!--buttons: edit-->
        <div id="client-description-edit" class="p-t-20 text-right">
            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                id="client-description-button-edit">{{ cleanLang(__('lang.edit_description')) }}</button>
        </div>

        <!--button: subit & cancel-->
        <div id="client-description-submit" class="p-t-20 hidden text-right">
            <button type="button" class="btn waves-effect waves-light btn-xs btn-default"
                id="client-description-button-cancel">{{ cleanLang(__('lang.cancel')) }}</button>
            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" data-type="form"
                data-form-id="client-details-container" data-ajax-type="post"
                data-url="{{ url('clients/'.$client->client_id .'/client-details') }}"
                id="client-description-button-save">{{ cleanLang(__('lang.save')) }}</button>
        </div>
        @endif

    </div>
</div>