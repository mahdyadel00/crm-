<div class="row">
    <div class="col-lg-12">
        <!--meta data - creatd by-->
        @if(isset($page['section']) && $page['section'] == 'edit')
        <div class="modal-meta-data">
            <small><strong>{{ cleanLang(__('lang.created_by')) }}:</strong>
                {{ $lead->first_name ?? runtimeUnkownUser() }} |
                {{ runtimeDate($lead->lead_created) }}</small>
        </div>
        @endif

        <!--title-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.lead_title')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="lead_title" name="lead_title" placeholder=""
                    value="{{ $lead->lead_title ?? '' }}">
            </div>
        </div>
        <!--first name-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.first_name')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="lead_firstname" name="lead_firstname"
                    placeholder="" value="{{ $lead->lead_firstname ?? '' }}">
            </div>
        </div>
        <!--last name-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.last_name')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="lead_lastname" name="lead_lastname"
                    placeholder="" value="{{ $lead->lead_lastname ?? '' }}">
            </div>
        </div>
        <!--telephone-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.telephone')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="lead_phone" name="lead_phone" placeholder=""
                    value="{{ $lead->lead_phone ?? '' }}">
            </div>
        </div>
        <!--email-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.email_address')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="lead_email" name="lead_email" placeholder=""
                    value="{{ $lead->lead_email ?? '' }}">
            </div>
        </div>


        <!--value-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.lead_value')) }} ({{
                            config('system.settings_system_currency_symbol') }})</label>
            <div class="col-sm-12 col-lg-9">
                <input type="number" class="form-control form-control-sm" id="lead_value" name="lead_value"
                    placeholder="" value="{{ $lead->lead_value ?? '' }}">
            </div>
        </div>



        <!--assigned [roles]-->
        @if(config('visibility.lead_modal_assign_fields'))
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.assigned')) }}</label>
            <div class="col-sm-12 col-lg-9">
                <select name="assigned" id="assigned"
                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">

                    <!--array of assigned-->
                    @if(isset($page['section']) && $page['section'] == 'edit' && isset($lead->assigned))
                    @foreach($lead->assigned as $user)
                    @php $assigned[] = $user->id; @endphp
                    @endforeach
                    @endif
                    <!--/#array of assigned-->
                    <!--users list-->
                    @foreach(config('system.team_members') as $user)
                    <option value="{{ $user->id }}" {{ runtimePreselectedInArray($user->id ?? '', $assigned ?? []) }}>{{
                                        $user->full_name }}</option>
                    @endforeach
                    <!--/#users list-->
                </select>
            </div>
        </div>
        @endif




        <!--status-->
        @if(request('status') != '' && array_key_exists(request('status'), config('system.lead_statuses')))
        <input type="hidden" name="lead_status" value="{{ request('status') }}">
        @else
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.status')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="lead_status" name="lead_status">
                    @foreach($statuses as $status)
                    <option value="{{ $status->leadstatus_id }}"
                        {{ runtimePreselected($lead->lead_status ?? '', $status->leadstatus_id) }}>{{
                                                    runtimeLang($status->leadstatus_title) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!--CUSTOMER FIELDS [expanded]-->
        @if(config('system.settings_customfields_display_leads') == 'expanded')
        @include('misc.customfields')
        @endif
        <!--/#CUSTOMER FIELDS [expanded]-->

        <!--lead details - toggle-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.details')) }}</span>
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_leads1" id="show_more_settings_leads1"
                            class="js-switch-toggle-hidden-content" data-target="add_lead_details">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--lead details - toggle-->
        <!--lead details-->
        <div class="hidden" id="add_lead_details">
            <!--description-->
            <div class="form-group row">
                <label
                    class="col-sm-12 text-left control-label col-form-label">{{ cleanLang(__('lang.notes')) }}</label>
                <div class="col-sm-12">
                    <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="lead_description"
                        id="lead_description">
                            {{ $lead->lead_description ?? '' }}
                    </textarea>
                </div>
            </div>

            <!--lead sources-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.source')) }}</label>
                @if(config('system.settings_leads_allow_new_sources') == 'yes')
                <!--existing-->
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control form-control-sm  select2-new-options" id="lead_source"
                        name="lead_source">
                        <option value=""></option>
                        @foreach($sources as $source)
                        @php $sourcelist[] = $source->leadsources_title;@endphp
                        <option value="{{ $source->leadsources_title }}"
                            {{ runtimePreselected($lead->lead_source ?? '', $source->leadsources_title) }}>{{
                        $source->leadsources_title }}</option>
                        @endforeach
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        {{!! clean(runtimeLeadSourceCustom($sourcelist, $lead->lead_source  ?? '')) !!}}
                        @endif
                    </select>
                </div>
                <!--/#existing-->
                @else
                <!--existing-->
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control form-control-sm" id="lead_source" name="lead_source">
                        @foreach($sources as $source)
                        @php $sourcelist[] = $source->leadsources_title;@endphp
                        <option value="{{ $source->leadsources_title }}"
                            {{ runtimePreselected($lead->lead_source ?? '', $source->leadsources_title) }}>{{
                        $source->leadsources_title }}</option>
                        @endforeach
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        {{!! clean(runtimeLeadSourceCustom($sourcelist, $lead->lead_source ?? '')) !!}}
                        @endif
                    </select>
                </div>
                <!--/#existing-->
                @endif
            </div>

            <!--lead category-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.category')) }}*</label>
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control form-control-sm" id="lead_categoryid"
                        name="lead_categoryid">
                        @foreach($categories as $category)
                        <option value="{{ $category->category_id }}"
                            {{ runtimePreselected($lead->lead_categoryid ?? '', $category->category_id) }}>{{
                                        runtimeLang($category->category_name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <!--tags-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <select name="tags" id="tags"
                        class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                        multiple="multiple" tabindex="-1" aria-hidden="true">
                        <!--array of selected tags-->
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        @foreach($lead->tags as $tag)
                        @php $selected_tags[] = $tag->tag_title ; @endphp
                        @endforeach
                        @endif
                        <!--/#array of selected tags-->
                        @foreach($tags as $tag)
                        <option value="{{ $tag->tag_title }}"
                            {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>
                            {{ $tag->tag_title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>



            <!--contacted-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.last_contacted')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm pickadate" autocomplete="off"
                        name="lead_last_contacted"
                        value="{{ runtimeDatepickerDate($lead->lead_last_contacted ?? '') }}">
                    <!--mysql date format-->
                    <input class="mysql-date" type="hidden" id="lead_last_contacted" name="lead_last_contacted"
                        value="{{ $lead->lead_last_contacted ?? '' }}">
                </div>
            </div>

            <div class="line"></div>
        </div>
        <!--lead details-->



        <!--CUSTOMER FIELDS [collapsed]-->
        @if(config('system.settings_customfields_display_leads') == 'toggled')
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.more_information')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_other" id="add_client_option_other"
                            class="js-switch-toggle-hidden-content" data-target="leads_custom_fields_collaped">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div id="leads_custom_fields_collaped" class="hidden">
            @if(config('app.application_demo_mode'))
            <!--DEMO INFO-->
            <div class="alert alert-info">
                <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5> 
                These are custom fields. You can change them or <a href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
            </div>
            @endif
            
            @include('misc.customfields')
        </div>
        @endif
        <!--/#CUSTOMER FIELDS [collapsed]-->



        <!--address and organisation - toggle-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.address_and_organisation_details')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_leads2" id="show_more_settings_leads2"
                            class="js-switch-toggle-hidden-content" data-target="add_lead_address_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--address and organisation - toggle-->

        <!--address and organisation-->
        <div class="hidden" id="add_lead_address_section">

            <!--company name-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.company_name')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_company_name"
                        name="lead_company_name" placeholder="" value="{{ $lead->lead_company_name ?? '' }}">
                </div>
            </div>

            <!--street-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.street')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_street" name="lead_street"
                        placeholder="" value="{{ $lead->lead_street ?? '' }}">
                </div>
            </div>
            <!--city-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.city')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_city" name="lead_city"
                        placeholder="" value="{{ $lead->lead_city ?? '' }}">
                </div>
            </div>
            <!--state-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.state')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_state" name="lead_state"
                        placeholder="" value="{{ $lead->lead_state ?? '' }}">
                </div>
            </div>
            <!--zip-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.zipcode')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_zip" name="lead_zip" placeholder=""
                        value="{{ $lead->lead_zip ?? '' }}">
                </div>
            </div>
            <!--country-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.country')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <select class="select2-basic form-control" id="lead_country" name="lead_country">
                        <option></option>
                        @include('misc.country-list')
                    </select>
                </div>
            </div>
            <!--website-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.website')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm" id="lead_website" name="lead_website"
                        placeholder="" value="{{ $lead->lead_website ?? '' }}">
                </div>
            </div>
            <div class="line"></div>
        </div>
        <!--address and organisation-->

        <!--editing only - content id-->
        <input type="hidden" name="edit_leads_id" value="">
        <!--/#editing only - content id-->

        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">

        <!--redirect to project-->
        @if(config('visibility.lead_show_lead_option'))
        <div class="line"></div>
        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="show_after_adding" name="show_after_adding"
                    class="filled-in chk-col-light-blue" checked="checked">
                <label for="show_after_adding">{{ cleanLang(__('lang.show_lead_after_adding')) }}</label>
            </div>
        </div>
        @endif

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>