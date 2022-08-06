        <!--HEADER-->
        <div class="billing-mode-only-item">
            <span class="pull-left">
                <h3><b>{{ cleanLang(__('lang.estimate')) }}</b>
                </h3>
                <span>
                    <h5>#{{ $bill->formatted_bill_estimateid }}</h5>
                </span>
            </span>
            <!--status-->
            <span class="pull-right">
                <!--draft-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('draft', $bill->bill_status) }}"
                    id="estimate-status-draft">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('draft', 'text') }} muted">{{ cleanLang(__('lang.draft')) }}</h1>
                </span>
                <!--new-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('new', $bill->bill_status) }}"
                    id="estimate-status-new">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('new', 'text') }}">{{ cleanLang(__('lang.new')) }}</h1>
                </span>
                <!--accepted-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('accepted', $bill->bill_status) }}"
                    id="estimate-status-accpeted">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('accepted', 'text') }}">{{ cleanLang(__('lang.accepted')) }}</h1>
                </span>
                <!--declined-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('declined', $bill->bill_status) }}"
                    id="estimate-status-declined">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('declined', 'text') }}">{{ cleanLang(__('lang.declined')) }}</h1>
                </span>
                <!--revised-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('revised', $bill->bill_status) }}"
                    id="estimate-status-revised">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('revised', 'text') }}">{{ cleanLang(__('lang.revised')) }}</h1>
                </span>
                <!--expired-->
                <span class="js-estimate-statuses {{ runtimeEstimateStatus('expired', $bill->bill_status) }}"
                    id="estimate-status-expired">
                    <h1 class="text-uppercase {{ runtimeEstimateStatusColors('expired', 'text') }}">{{ cleanLang(__('lang.expired')) }}</h1>
                </span>
            </span>
        </div>