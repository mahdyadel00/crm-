<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for emails
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\User;

class EmailerRepository {

    /**
     * Inject dependecies
     */
    public function __construct(User $user) {

    }

    /**
     * get core data used in all templates
     * @return array
     */
    public function coreData() {

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

        //create common data
        $data = [
            'our_company_name' => config('system.settings_company_name'),
            'todays_date' => runtimeDate(date('Y-m-d')),
            'email_signature' => $email_signature,
            'email_footer' => $email_footer,
            'dashboard_url' => url('/'),
        ];

        //return
        return $data;
    }

    /**
     * [transactional email]
     * send email to clients about new project created
     * @param array $users list of user id's of email recipients
     * @param array $data array of passed data
     * @param object $payload model object (optional)
     */
    public function newProjectCreated($users, $data = [], $payload = '') {

    }

}