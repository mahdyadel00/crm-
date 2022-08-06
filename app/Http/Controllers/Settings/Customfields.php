<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for template settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Customfields\DestroyResponse;
use App\Http\Responses\Settings\Customfields\IndexResponse;
use App\Http\Responses\Settings\Customfields\StandardFormResponse;
use App\Http\Responses\Settings\Customfields\UpdateResponse;
use App\Repositories\CustomFieldsRepository;
use Illuminate\Http\Request;

class Customfields extends Controller {

    /**
     * The customrepo repository instance.
     */
    protected $customrepo;

    public function __construct(CustomFieldsRepository $customrepo) {

        //parent
        parent::__construct();

        $this->customrepo = $customrepo;

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function showClient() {

        //set typs
        request()->merge([
            'customfields_type' => 'clients',
        ]);

        if (!request()->filled('filter_field_type')) {
            request()->merge([
                'filter_field_type' => 'text',
            ]);
        }

        //crumbs, page data & stats
        $page = $this->pageSettings('clients');

        $fields = $this->customrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
            'tab_menu_type' => 'clients',
            'save_button_url' => url('/settings/customfields/clients?filter_field_type=') . request('filter_field_type'),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateClient() {

        //update each field
        foreach (request('customfields_title') as $key => $value) {
            //save or delete/reset the field
            if ($value != '') {
                \App\Models\CustomField::where('customfields_id', $key)
                    ->update([
                        'customfields_title' => $_POST['customfields_title'][$key],
                        'customfields_show_client_page' => runtimeDBCheckBoxYesNo($_POST['customfields_show_client_page'][$key]),
                        'customfields_show_invoice' => runtimeDBCheckBoxYesNo($_POST['customfields_show_invoice'][$key]),
                        'customfields_show_filter_panel' => runtimeDBCheckBoxYesNo($_POST['customfields_show_filter_panel'][$key]),
                        'customfields_standard_form_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_standard_form_status'][$key]),
                        'customfields_datapayload' => json_encode($_POST['customfields_datapayload'][$key] ?? []),
                        'customfields_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_status'][$key]),
                        'customfields_sorting_a_z' => 'a',
                    ]);
            } else {
                $this->deleteField($key);
            }
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function showProject() {

        //set typs
        request()->merge([
            'customfields_type' => 'projects',
        ]);

        if (!request()->filled('filter_field_type')) {
            request()->merge([
                'filter_field_type' => 'text',
            ]);
        }

        //crumbs, page data & stats
        $page = $this->pageSettings('projects');

        $fields = $this->customrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
            'tab_menu_type' => 'projects',
            'save_button_url' => url('/settings/customfields/projects?filter_field_type=') . request('filter_field_type'),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProject() {

        //update or delete each field
        foreach (request('customfields_title') as $key => $value) {
            //save or delete/reset the field
            if ($value != '') {
                \App\Models\CustomField::where('customfields_id', $key)
                    ->update([
                        'customfields_title' => $_POST['customfields_title'][$key],
                        'customfields_show_project_page' => runtimeDBCheckBoxYesNo($_POST['customfields_show_project_page'][$key]),
                        'customfields_show_filter_panel' => runtimeDBCheckBoxYesNo($_POST['customfields_show_filter_panel'][$key]),
                        'customfields_standard_form_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_standard_form_status'][$key]),
                        'customfields_datapayload' => json_encode($_POST['customfields_datapayload'][$key] ?? []),
                        'customfields_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_status'][$key]),
                        'customfields_sorting_a_z' => 'a',
                    ]);
            } else {
                $this->deleteField($key);
            }
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function showLead() {

        //set typs
        request()->merge([
            'customfields_type' => 'leads',
        ]);

        if (!request()->filled('filter_field_type')) {
            request()->merge([
                'filter_field_type' => 'text',
            ]);
        }

        //crumbs, page data & stats
        $page = $this->pageSettings('leads');

        $fields = $this->customrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
            'tab_menu_type' => 'leads',
            'save_button_url' => url('/settings/customfields/leads?filter_field_type=') . request('filter_field_type'),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLead() {

        //update - text fields
        foreach (request('customfields_title') as $key => $value) {
            //save or delete/reset the field
            if ($value != '') {
                \App\Models\CustomField::where('customfields_id', $key)
                    ->update([
                        'customfields_title' => $_POST['customfields_title'][$key],
                        'customfields_show_lead_summary' => runtimeDBCheckBoxYesNo($_POST['customfields_show_lead_summary'][$key]),
                        'customfields_show_filter_panel' => runtimeDBCheckBoxYesNo($_POST['customfields_show_filter_panel'][$key]),
                        'customfields_standard_form_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_standard_form_status'][$key]),
                        'customfields_datapayload' => json_encode($_POST['customfields_datapayload'][$key] ?? []),
                        'customfields_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_status'][$key]),
                        'customfields_sorting_a_z' => 'a',
                    ]);
            } else {
                $this->deleteField($key);
            }
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function showTask() {

        //set typs
        request()->merge([
            'customfields_type' => 'tasks',
        ]);

        if (!request()->filled('filter_field_type')) {
            request()->merge([
                'filter_field_type' => 'text',
            ]);
        }

        //crumbs, page data & stats
        $page = $this->pageSettings('tasks');

        $fields = $this->customrepo->search();

        //reponse payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
            'tab_menu_type' => 'tasks',
            'save_button_url' => url('/settings/customfields/tasks?filter_field_type=') . request('filter_field_type'),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTask() {

        //update - text fields
        foreach (request('customfields_title') as $key => $value) {
            //save or delete/reset the field
            if ($value != '') {
                \App\Models\CustomField::where('customfields_id', $key)
                    ->update([
                        'customfields_title' => $_POST['customfields_title'][$key],
                        'customfields_show_task_summary' => runtimeDBCheckBoxYesNo($_POST['customfields_show_task_summary'][$key]),
                        'customfields_show_filter_panel' => runtimeDBCheckBoxYesNo($_POST['customfields_show_filter_panel'][$key]),
                        'customfields_standard_form_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_standard_form_status'][$key]),
                        'customfields_datapayload' => json_encode($_POST['customfields_datapayload'][$key] ?? []),
                        'customfields_status' => runtimeDBCheckBoxEnabledDisabled($_POST['customfields_status'][$key]),
                        'customfields_sorting_a_z' => 'a',
                    ]);
            } else {
                $this->deleteField($key);
            }
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }

    /**
     * Delete a customfield
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {

        //delete/reset the field
        $this->deleteField($id);

        //generate a response
        return new DestroyResponse(['id' => $id]);

    }

    /**
     * Delete a customfield
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteField($id = '') {

        //validate
        if (!is_numeric($id)) {
            return false;
        }

        //delete (actually just resetting data)
        if ($field = \App\Models\CustomField::Where('customfields_id', $id)->first()) {
            $field->customfields_datapayload = '';
            $field->customfields_title = '';
            $field->customfields_required = 'no';
            $field->customfields_standard_form_status = 'disabled';
            $field->customfields_status = 'disabled';
            $field->customfields_show_client_page = null;
            $field->customfields_show_project_page = null;
            $field->customfields_show_task_summary = null;
            $field->customfields_show_lead_summary = null;
            $field->customfields_show_invoice = null;
            $field->customfields_sorting_a_z = 'z';
            $field->save();
        }

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function showStandardForm() {

        //crumbs, page data & stats
        $page = $this->pageSettings('standard-form');

        //set typs
        request()->merge([
            'customfields_type' => request('tab_menu_type'),
            'filter_show_standard_form_status' => 'enabled',
            'filter_field_status' => 'enabled',
            'sort_by' => 'customfields_position',
        ]);

        //show all fields
        config(['settings.custom_fields_display_limit' => 1000]);

        //get fieelds
        $fields = $this->customrepo->search();

        //get diplay settings
        switch (request('tab_menu_type')) {
        case 'leads':
            $display_setting = config('system.settings_customfields_display_leads');
            break;
        case 'tasks':
            $display_setting = config('system.settings_customfields_display_tasks');
            break;
        case 'projects':
            $display_setting = config('system.settings_customfields_display_projects');
            break;
        case 'clients':
            $display_setting = config('system.settings_customfields_display_clients');
            break;
        }

        //reponse payload
        $payload = [
            'page' => $page,
            'fields' => $fields,
            'display_setting' => $display_setting,
            'tab_menu_type' => request('tab_menu_type'),
        ];

        //show the view
        return new StandardFormResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStandardFormRequired() {

        //update the custom fields required
        foreach (request('sort-fields') as $key => $value) {
            //save or delete/reset the field
            \App\Models\CustomField::where('customfields_id', $key)
                ->update([
                    'customfields_required' => runtimeDBCheckBoxYesNo($_POST['customfields_required'][$key]),
                ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateDisplaySettings() {

        //selected state
        $state = (request('custom_fields_display_setting') == 'on') ? 'toggled' : 'expanded';

        //update the appropriate setings field
        switch (request('tab_menu_type')) {
        case 'leads':
            \App\Models\Settings::where('settings_id', 1)
                ->update([
                    'settings_customfields_display_leads' => $state,
                ]);
            break;
        case 'tasks':
            \App\Models\Settings::where('settings_id', 1)
                ->update([
                    'settings_customfields_display_tasks' => $state,
                ]);
            break;
        case 'projects':
            \App\Models\Settings::where('settings_id', 1)
                ->update([
                    'settings_customfields_display_projects' => $state,
                ]);
            break;
        case 'clients':
            \App\Models\Settings::where('settings_id', 1)
                ->update([
                    'settings_customfields_display_clients' => $state,
                ]);
            break;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateFieldPositions() {

        //update the custom fields required
        $count = 1;
        foreach (request('sort-fields') as $key => $value) {
            //save or delete/reset the field
            \App\Models\CustomField::where('customfields_id', $key)
                ->update([
                    'customfields_position' => $count,
                ]);
            $count++;
        }
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => ' - ' . __('lang.settings'),
            'heading' => __('lang.settings'),
            'settingsmenu_general' => 'active',
        ];

        if ($section == 'clients') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.clients'),
                __('lang.custom_form_fields'),
            ];
        }

        if ($section == 'projects') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.projects'),
                __('lang.custom_form_fields'),
            ];
        }

        if ($section == 'tasks') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.tasks'),
                __('lang.custom_form_fields'),
            ];
        }

        if ($section == 'leads') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.leads'),
                __('lang.custom_form_fields'),
            ];
        }

        if ($section == 'standard-form') {
            $page['crumbs'] = [
                __('lang.settings'),
                __('lang.custom_form_fields'),
                __('lang.standard_form'),
            ];
        }

        //shpw tabs menu
        config(['visibility.settings_customfileds_tabs_menu' => true]);
        return $page;
    }

}
