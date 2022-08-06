<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\FormBuilder\BuildFormResponse;
use App\Http\Responses\Settings\FormBuilder\EmbedResponse;
use App\Http\Responses\Settings\FormBuilder\SaveFormResponse;
use App\Http\Responses\Settings\FormBuilder\SettingsResponse;
use App\Repositories\WebformRepository;
use Illuminate\Http\Request;
use Validator;

class Formbuilder extends Controller {

    /**
     * The webform repository instance.
     */
    protected $webformrepo;

    public function __construct(WebformRepository $webformrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->webformrepo = $webformrepo;

    }

    /**
     * Display general form
     *
     * @return \Illuminate\Http\Response
     */
    public function buildForm($id) {

        //crumbs, page data & stats
        $page = $this->pageSettings('builder');

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        //get all the available lead form fields
        $custom_fields = $this->availableFormFields();

        //double json decode
        $form = json_decode($webform->webform_builder_payload, true);
        $current_fields = (json_decode($form, true));

        //reponse payload
        $payload = [
            'page' => $page,
            'webform' => $webform,
            'custom_fields' => $custom_fields,
            'current_fields' => $current_fields,
        ];

        //show the view
        return new BuildFormResponse($payload);
    }

    /**
     * Display general form
     *
     * @return \Illuminate\Http\Response
     */
    public function saveForm($id) {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        $first_last_name = 0;
        $fields = json_decode(request('webform-builder-payload'));
        foreach ($fields as $field) {
            if (isset($field->name) && ($field->name == 'lead_firstname' || $field->name == 'lead_lastname')) {
                $first_last_name++;
            }
        }
        if ($first_last_name != 2) {
            abort(409, __('lang.lead_first_last_name_required'));
        }

        //update webform
        $webform->webform_builder_payload = json_encode(request('webform-builder-payload'));
        $webform->save();

        //reponse payload
        $payload = [
            'page' => $page,
            'webform' => $webform,
        ];

        //show the view
        return new SaveFormResponse($payload);
    }

    /**
     * Get all the enabled lead custom fields and render then as an array that
     * can be used in the frontend javascript
     */
    public function availableFormFields() {

        $obj = [];

        //get the enabled custom fields
        $fields = \App\Models\CustomField::Where('customfields_type', 'leads')
            ->Where('customfields_status', 'enabled')
            ->orderBy('customfields_name', 'DESC')->get();

        //create javascrript ready array
        foreach ($fields as $field) {

            //match our field types to formbuilder.js types
            $match_types = [
                'text' => 'text',
                'paragraph' => 'textarea',
                'number' => 'number',
                'decimal' => 'number',
                'date' => 'date',
                'checkbox' => 'checkbox-group',
                'dropdown' => 'select',
            ];

            //dorp down fields
            $dropdown_value = [];
            if ($field->customfields_datatype == 'dropdown') {
                //get list from db and create formbuilder array
                $lists = json_decode($field->customfields_datapayload);
                foreach ($lists as $list) {
                    $option = [
                        'label' => $list,
                        'value' => $list,
                        'selected' => false,
                    ];
                    array_push($dropdown_value, $option);
                }
            }

            //append to thearray
            $arr = [
                'name' => $field->customfields_name,
                'label' => $field->customfields_title,
                'attrs' => [
                    'type' => $match_types[$field->customfields_datatype],
                ],
                'icon' => 'formbuilder-custom-field-icon ' . $field->customfields_name,
                'className' => 'form-control',
                'values' => $dropdown_value,
            ];

            array_push($obj, $arr);
        }

        //start with the files files
        $arr = [
            'name' => 'attachments',
            'label' => __('lang.attachments'),
            'attrs' => [
                'type' => 'file',
            ],
            'icon' => 'mdi mdi-cloud-upload display-inline-block p-l-10 p-r-15 formbuilder-custom-field-icon attachments',
            'className' => 'form-control webform_file_upload',
            'values' => [],
            'placeholder' => __('lang.drag_drop_file'),
        ];
        array_push($obj, $arr);

        //add email_address (default fields)
        $arr = [
            'name' => 'lead_email',
            'label' => __('lang.email_address'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_email',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_phone (default fields)
        $arr = [
            'name' => 'lead_phone',
            'label' => __('lang.telephone'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_phone',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_job_position (default fields)
        $arr = [
            'name' => 'lead_job_position',
            'label' => __('lang.job_title'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_job_position',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_company_name (default fields)
        $arr = [
            'name' => 'lead_company_name',
            'label' => __('lang.company_name'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_company_name',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_website (default fields)
        $arr = [
            'name' => 'lead_website',
            'label' => __('lang.website'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_website',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_street (default fields)
        $arr = [
            'name' => 'lead_street',
            'label' => __('lang.street'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_street',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_city (default fields)
        $arr = [
            'name' => 'lead_city',
            'label' => __('lang.city'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_city',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_state (default fields)
        $arr = [
            'name' => 'lead_state',
            'label' => __('lang.state'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_state',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_zip (default fields)
        $arr = [
            'name' => 'lead_zip',
            'label' => __('lang.zipcode'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_zip',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add lead_country (default fields)
        $arr = [
            'name' => 'lead_country',
            'label' => __('lang.country'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_country',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add the last name (required fields)
        $arr = [
            'name' => 'lead_lastname',
            'label' => __('lang.last_name'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_lastname required',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //add the first name (required fields)
        $arr = [
            'name' => 'lead_firstname',
            'label' => __('lang.first_name'),
            'attrs' => [
                'type' => 'text',
            ],
            'icon' => 'formbuilder-custom-field-icon lead_firstname required',
            'className' => 'form-control',
            'values' => [],
        ];
        array_push($obj, $arr);

        //return the array
        return $obj;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //custom error messages
        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_company_name' => 'required',
            'settings_system_date_format' => 'required',
            'settings_system_datepicker_format' => 'required',
            'settings_system_default_leftmenu' => 'required',
            'settings_system_default_statspanel' => 'required',
            'settings_system_pagination_limits' => 'required',
            'settings_system_currency_symbol' => 'required',
            'settings_system_currency_position' => 'required',
            'settings_system_close_modals_body_click' => 'required',
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //update
        if (!$this->settingsrepo->updateGeneral()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display general form
     *
     * @return \Illuminate\Http\Response
     */
    public function formSettings($id) {

        //crumbs, page data & stats
        $page = $this->pageSettings('settings');

        //get the form
        $webforms = $this->webformrepo->search($id);
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'webform' => $webform,
        ];

        //show the view
        return new SettingsResponse($payload);
    }

    /**
     * Save form settig=ngs
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSettings($id) {

        //validate - custom error messages
        $messages = [
            'webform_title.required' => __('lang.webform_title') . ' - ' . __('lang.is_required'),
            'webform_thankyou_message.required' => __('lang.thank_you_message') . ' - ' . __('lang.is_required'),
            'webform_submit_button_text.required' => __('lang.submit_button_text') . ' - ' . __('lang.is_required'),
            'webform_lead_title.required' => __('lang.lead_title') . ' - ' . __('lang.is_required'),
        ];

        //crumbs, page data & stats
        $page = $this->pageSettings('settings');

        //get the form
        $webforms = $this->webformrepo->search($id);
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        //validate
        $validator = Validator::make(request()->all(), [
            'webform_title' => [
                'required',
            ],
            'webform_thankyou_message' => [
                'required',
            ],
            'webform_submit_button_text' => [
                'required',
            ],
            'webform_lead_title' => [
                'required',
            ],
        ], $messages);

        //validation errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //save changes
        $webform->webform_title = request('webform_title');
        $webform->webform_thankyou_message = request('webform_thankyou_message');
        $webform->webform_notify_admin = request('webform_notify_admin');
        $webform->webform_submit_button_text = request('webform_submit_button_text');
        $webform->webform_lead_title = request('webform_lead_title');
        $webform->save();

        //response
        return response()->json(array('notification' => ['type' => 'success', 'value' => __('lang.request_has_been_completed')]));

    }

    /**
     * Display general form
     *
     * @return \Illuminate\Http\Response
     */
    public function embedCode($id) {

        //crumbs, page data & stats
        $page = $this->pageSettings('embed');

        //get the form
        $webforms = $this->webformrepo->search();
        if (!$webform = $webforms->first()) {
            abort(404);
        }

        //embed code
        $embed_code = '<iframe width="650" height="900" src="' . url('webform/embed/' . $webform->webform_uniqueid) . '" frameborder="0" allowfullscreen></iframe>';

        //direct url
        $direct_url = url('webform/view/' . $webform->webform_uniqueid);

        //reponse payload
        $payload = [
            'page' => $page,
            'webform' => $webform,
            'embed_code' => $embed_code,
            'direct_url' => $direct_url,
        ];

        //show the view
        return new EmbedResponse($payload);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.web_forms'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => ' - ' . __('lang.settings'),
            'heading' => __('lang.settings'),
        ];

        if ($section == 'builder') {
            $page += [
                'menutab_builder' => 'active',
            ];
            return $page;
        }

        if ($section == 'settings') {
            $page += [
                'menutab_settings' => 'active',
            ];
            return $page;
        }

        if ($section == 'embed') {
            $page += [
                'menutab_embed' => 'active',
            ];
            return $page;
        }

        return $page;
    }

}
