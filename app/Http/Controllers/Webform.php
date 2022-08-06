<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for webforms
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Responses\Webform\SaveResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\WebformRepository;

class Webform extends Controller {

    /**
     * The settings repository instance.
     */
    protected $webformrepo;
    protected $attachmentrepo;

    public function __construct(
        WebformRepository $webformrepo,
        AttachmentRepository $attachmentrepo
    ) {

        //parent
        parent::__construct();

        $this->webformrepo = $webformrepo;
        $this->attachmentrepo = $attachmentrepo;

    }

    /**
     * Display Webform in browser
     *
     * @return \Illuminate\Http\Response
     */
    public function showWeb() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            config(['visibility.webform' => 'error']);
        }

        //get field
        $fields = $this->formFields($webform);

        //show form
        config(['visibility.webform' => 'show']);
        config(['visibility.webform_view' => request()->segment(2)]);

        //show the view
        return view('pages/webform/form', compact('fields', 'webform'));
    }

    /**
     * Display Webform in browser
     *
     * @return \Illuminate\Http\Response
     */
    public function saveForm() {

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            config(['visibility.webform' => 'error']);
        }

        //get field
        $fields = $this->formFieldsArray($webform);

        //validate required fields
        $errors = 0;
        $error_message = '';
        foreach ($fields as $field) {
            if ($field['required'] && request($field['name']) == '') {
                $error_message .= '<li>' . $field['label'] . '</li>';
                $errors++;
            }
        }
        if ($errors > 0) {
            return new SaveResponse(['type' => 'error-required-fields', 'error_message' => $error_message]);
        }

        //get the last row (order by position - desc)
        if ($last = \App\Models\Lead::orderBy('lead_position', 'desc')->first()) {
            $position = $last->lead_position + config('settings.db_position_increment');
        } else {
            //default position increment
            $position = config('settings.db_position_increment');
        }

        //create lead with default database fields
        $lead = new \App\Models\Lead();
        $lead->lead_firstname = request('lead_firstname');
        $lead->lead_lastname = request('lead_lastname');
        $lead->lead_position = $position;
        $lead->lead_email = request('lead_email');
        $lead->lead_phone = request('lead_phone');
        $lead->lead_job_position = request('lead_job_position');
        $lead->lead_company_name = request('lead_company_name');
        $lead->lead_website = request('lead_website');
        $lead->lead_street = request('lead_street');
        $lead->lead_city = request('lead_city');
        $lead->lead_state = request('lead_state');
        $lead->lead_zip = request('lead_zip');
        $lead->lead_country = request('lead_country');
        $lead->lead_title = ($webform->webform_lead_title != '') ? $webform->webform_lead_title : request('lead_firstname') . ' ' . request('lead_lastname');
        $lead->lead_creatorid = 0;
        $lead->save();

        //save every other possible field
        for ($i = 1; $i <= 150; $i++) {
            $name = "lead_custom_field_$i";
            $lead->{$name} = request($name); //curly brackets for dynamic naming
        }

        //save
        $lead->save();

        //[save attachments] loop through and save each attachment
        if (request()->filled('attachments')) {
            foreach (request('attachments') as $uniqueid => $file_name) {
                $data = [
                    'attachment_creatorid' => 0,
                    'attachment_clientid' => request('expense_clientid'),
                    'attachmentresource_type' => 'lead',
                    'attachmentresource_id' => $lead->lead_id,
                    'attachment_directory' => $uniqueid,
                    'attachment_uniqiueid' => $uniqueid,
                    'attachment_filename' => $file_name,
                ];
                //process and save to db
                $this->attachmentrepo->process($data);
            }
        }

        //increase for counter
        $webform->webform_submissions = $webform->webform_submissions + 1;
        $webform->save();

        /** ----------------------------------------------
         * send email to admin users
         * ----------------------------------------------*/
        if ($webform->webform_notify_admin == 'yes') {
            $data = [
                'lead_form_name' => $webform->webform_title,
            ];
            if ($users = \App\Models\User::Where('role_id', 1)->get()) {
                foreach ($users as $user) {
                    $mail = new \App\Mail\LeadNewSubmission($user, $data, $lead);
                    $mail->build();
                }
            }

        }

        //payload
        $payload = [
            'type' => 'success',
            'webform' => $webform,
        ];

        //show the view
        return new SaveResponse($payload);

    }

    /**
     * return an array of the form fields
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formFieldsArray($webform) {

        //payload
        $obj = [];

        //valid fields
        $valid = [
            'text',
            'textarea',
            'date',
            'number',
            'select',
            'checkbox-group',
            'file',
        ];

        //get the json form payload
        $fields = json_decode(json_decode($webform->webform_builder_payload));

        //extrach the form field names and their required states
        foreach ($fields as $field) {
            if (in_array($field->type, $valid)) {
                $var = [
                    'name' => $field->name,
                    'required' => $field->required,
                    'label' => $field->label,
                ];
                //force first name and last name to be required
                if ($field->name == 'lead_firstname' || $field->name == 'lead_lastname') {
                    $var['required'] = true;
                }
                array_push($obj, $var);
            }
        }

        return $obj;
    }

    /**
     * create the html for all the form fields
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function formFields($webform) {

        //payload
        $html = '';

        //get the json form data
        $fields = json_decode(json_decode($webform->webform_builder_payload));
        //dd($fields);

        foreach ($fields as $field) {

            $payload = [
                'label' => html_entity_decode($field->label),
                'name' => $field->name ?? '',
                'class' => $field->className ?? '',
                'required' => $field->required ?? '',
                'placeholder' => html_entity_decode($field->placeholder ?? ''),
                'tooltip' => html_entity_decode($field->description ?? ''),
            ];

            //create text field
            if ($field->type == 'text') {
                //force first name and last name to be required
                if ($field->name == 'lead_firstname' || $field->name == 'lead_lastname') {
                    $payload['required'] = true;
                }
                $html .= view('pages/webform/elements/text', compact('payload'))->render();
            }

            //create textarea field
            if ($field->type == 'textarea') {
                $html .= view('pages/webform/elements/textarea', compact('payload'))->render();
            }

            //create date field
            if ($field->type == 'date') {
                $html .= view('pages/webform/elements/date', compact('payload'))->render();
            }

            //create number field
            if ($field->type == 'number') {
                $html .= view('pages/webform/elements/number', compact('payload'))->render();
            }

            //create select field
            if ($field->type == 'select') {
                $options = '';
                //create dropdown
                foreach ($field->values as $value) {
                    $options .= '<option value="' . $value->value . '">' . $value->label . '</option>';
                }
                $payload['options'] = $options;
                $html .= view('pages/webform/elements/dropdown', compact('payload'))->render();
            }

            //create checkbox field
            if ($field->type == 'checkbox-group') {
                $html .= view('pages/webform/elements/checkbox', compact('payload'))->render();
            }

            //create file field
            if ($field->type == 'file') {
                $html .= view('pages/webform/elements/attachments', compact('payload'))->render();
            }

            //create header field
            if ($field->type == 'header') {
                $payload = [
                    'label' => html_entity_decode($field->label),
                ];
                $html .= view('pages/webform/elements/header', compact('payload'))->render();
            }

            //create paragraph field
            if ($field->type == 'paragraph') {
                $payload = [
                    'label' => html_entity_decode($field->label),
                ];
                $html .= view('pages/webform/elements/paragraph', compact('payload'))->render();
            }

        }

        return $html;

    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'page' => 'webform',
            'meta_title' => __('lang.settings'),
        ];

        return $page;
    }

}
