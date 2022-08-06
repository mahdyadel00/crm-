@if($show == 'form')
@if(count($products) > 0)

<!--stripe product-->
<div class="form-group row">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">@lang('lang.product') <a
            class="align-middle font-14 toggle-collapse" href="#stripe_products_info" role="button"><i
                class="ti-info-alt text-themecontrast"></i></a></label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic stripe_product_price form-control form-control-sm dynamic-select2-product"
            id="subscription_gateway_product" data-placeholder="@lang('lang.select')"
            name="subscription_gateway_product" data-prices-dropdown="subscription_gateway_price">
            <option></option>
            @foreach($products as $product)
            @if($product['id'] != 'dashboard_invoice_default_do_not_delete')
            <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
            @endif
            @endforeach
        </select>
    </div>
</div>


<!--stripe price-->
<div class="form-group row">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.plan')) }}
        <a class="align-middle font-14 toggle-collapse" href="#stripe_products_info" role="button"><i
                class="ti-info-alt text-themecontrast"></i></a></label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm dynamic-select2-price" id="subscription_gateway_price"
            name="subscription_gateway_price" disabled>
        </select>
    </div>
</div>


<!--/#project manager-->
<div class="collapse" id="stripe_products_info">
    <div class="alert alert-info">{{ cleanLang(__('lang.stripe_products_info')) }}</div>
</div>

<!--client and project-->
@if(config('visibility.subscription_modal_client_project_fields'))
<!--client-->
<div class="form-group row">
    <label
        class="col-sm-12 col-lg-3 text-left control-label col-form-label  required">{{ cleanLang(__('lang.client')) }}*</label>
    <div class="col-sm-12 col-lg-9">
        <!--select2 basic search-->
        <select name="subscription_clientid" id="subscription_clientid"
            class="clients_and_projects_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
            data-projects-dropdown="subscription_projectid" data-feed-request-type="clients_projects"
            data-ajax--url="{{ url('/') }}/feed/company_names">
        </select>
    </div>
</div>
<!--projects-->
<div class="form-group row">
    <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.project')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm dynamic_subscription_projectid"
            id="subscription_projectid" name="subscription_projectid" disabled>
        </select>
    </div>
</div>
@endif


<!--clients projects-->
@if(config('visibility.expense_modal_clients_projects'))
<div class="form-group row">
    <label for="example-month-input"
        class="col-sm-12 col-lg-3 col-form-label text-left">{{ cleanLang(__('lang.project')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm" id="expense_projectid" name="expense_projectid">
            @foreach(config('settings.clients_projects') as $project)
            <option value="{{ $project->project_id ?? '' }}">{{ $project->project_title }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif

<!--category-->
<div class="form-group row">
    <label for="example-month-input"
        class="col-sm-12 col-lg-3 col-form-label text-left required">{{ cleanLang(__('lang.category')) }}</label>
    <div class="col-sm-12 col-lg-9">
        <select class="select2-basic form-control form-control-sm" id="subscription_categoryid"
            name="subscription_categoryid">
            @foreach($categories as $category)
            <option value="{{ $category->category_id }}"
                {{ runtimePreselected($subscription->subscription_categoryid ?? '', $category->category_id) }}>{{
                                runtimeLang($category->category_name) }}</option>
            @endforeach
        </select>
    </div>
</div>

<!--notify client-->
<div class="form-group form-group-checkbox row">
    <div class="col-12 text-left p-t-5">
        <input type="checkbox" id="show_after_adding" name="send_email_to_customer" class="filled-in chk-col-light-blue"
            checked="checked">
        <label for="send_email_to_customer">{{ cleanLang(__('lang.send_email_to_client')) }}</label>
    </div>
</div>

@else
<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/products-not-found.svg" alt="404 - Not found" />
</div>
<div class="splash-text p-b-30">
    @lang('lang.stripe_products_not_found')
    </br>
    <a href="https://growcrm.io/documentation/subscription-plans/"
        target="_blank">@lang('lang.see_documentation_for_details')</a>
</div>
@endif
@endif

<!--error connecting to stripe-->
@if($show == 'error')
<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/general-error.png" alt="404 - Not found" />
</div>
<div class="splash-text">
    {{ $message }}
</div>
@endif



<!--error connecting to stripe-->
@if($show == 'no-products')
<div class="splash-image" id="updatePasswordSplash">
    <img src="{{ url('/') }}/public/images/products-not-found.svg" alt="404 - Not found" />
</div>
<div class="splash-text p-b-30">
    @lang('lang.stripe_products_not_found')
    </br>
    <a href="https://growcrm.io/documentation/subscription-plans/"
        target="_blank">@lang('lang.see_documentation_for_details')</a>
</div>
@endif