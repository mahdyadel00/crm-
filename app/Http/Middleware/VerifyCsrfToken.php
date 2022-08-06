<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware {
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'login',
        'webform',
        'webform/submit/*',
        'webform/fileupload',
        'api/stripe/webhooks',
        'api/mollie/webhooks',
        'api/paypal/ipn',
        'payments/thankyou',
        'payments/thankyou/razorpay',

        //[MT]
        'app-admin/webhooks/*',
        'app/settings/account/thankyou',
    ];
}
