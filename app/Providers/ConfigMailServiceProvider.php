<?php

/** --------------------------------------------------------------------------------
 * This service provider configures the applications email settings
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ConfigMailServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot() {

        //do not run this for SETUP path
        if (env('SETUP_STATUS') != 'COMPLETED') {
            return;
        }

        //get settings
        $settings = \App\Models\Settings::find(1);

        //defaults
        $email_signature = '';
        $email_footer = '';

        //get email signature
        if ($template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Email Signature')->first()) {
            $email_signature = $template->emailtemplate_body;
        }

        //get email footer
        if ($template = \App\Models\EmailTemplate::Where('emailtemplate_name', 'Email Footer')->first()) {
            $email_footer = $template->emailtemplate_body;
        }

        //save to config
        config([
            'mail.driver' => $settings->settings_email_server_type,
            'mail.host' => $settings->settings_email_smtp_host,
            'mail.port' => $settings->settings_email_smtp_port,
            'mail.username' => $settings->settings_email_smtp_username,
            'mail.password' => $settings->settings_email_smtp_password,
            'mail.encryption' => ($settings->settings_email_smtp_encryption == 'none') ? '' : $settings->settings_email_smtp_encryption,
            'mail.data' => [
                'our_company_name' => config('system.settings_company_name'),
                'todays_date' => runtimeDate(date('Y-m-d')),
                'email_signature' => $email_signature,
                'email_footer' => $email_footer,
                'dashboard_url' => url('/'),
            ],
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
