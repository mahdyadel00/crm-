<!-- Payments - This month-->
<div class="col-lg-3 col-md-6 click-url cursor-pointer" data-url="{{ url('payments/search?ref=list&filter_payment_date_start='.$payload['filter_payment_month_start'].'&filter_payment_date_end='.$payload['filter_payment_month_end']) }}">
    <div class="card">
        <div class="card-body p-l-15 p-r-15">
            <div class="d-flex p-10 no-block">
                <span class="align-slef-center">
                    <h2 class="m-b-0">{{ runtimeMoneyFormat($payload['payments']['this_month']) }}</h2>
                    <h6 class="text-muted m-b-0">{{ cleanLang(__('lang.payments')) }} - {{ cleanLang(__('lang.month')) }}</h6>
                </span>
                <div class="align-self-center display-6 ml-auto"><i class="text-info icon-Credit-Card2"></i></div>
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar bg-info w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                aria-valuemax="100"></div>
        </div>
    </div>
</div>