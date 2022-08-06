<div class="row">
    <div class="col-lg-12">

        <!--description-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.description')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <textarea class="w-100" id="item_description" rows="5" name="item_description"
                    >{{ $item->item_description ?? '' }}</textarea>
            </div>
        </div>


        <!--rate-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.rate')) }}*</label>
            <div class="col-sm-12 col-lg-9 input-group input-group-sm">
                <span class="input-group-addon">{{ config('system.settings_system_currency_symbol') }}</span>
                <input type="number" name="item_rate" id="item_rate" class="form-control form-control-sm"
                    value="{{ $item->item_rate ?? '' }}">
            </div>
        </div>
        <!--units-->
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.units')) }}*
                <span class="align-middle text-info font-16" data-toggle="tooltip" title="{{ cleanLang(__('lang.units_examples')) }}"
                    data-placement="top"><i class="ti-info-alt"></i></span></label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="item_unit" name="item_unit"
                    value="{{ $item->item_unit ?? '' }}">
            </div>
        </div>

        <!--category-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.category')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="item_categoryid" name="item_categoryid">
                    @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ runtimePreselected($item->item_categoryid ?? '', $category->category_id) }}>{{
                        runtimeLang($category->category_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>


        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">
        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>