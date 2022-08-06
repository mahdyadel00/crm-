<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the fault error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
     */

    'accepted' => ':attribute ' . __('lang.must_be_accepted'),
    'active_url' => ':attribute ' . __('lang.is_not_a_valid_url'),
    'after' => ':attribute ' . __('lang.date_is_not_valid'),
    'after_or_equal' => ':attribute ' . __('lang.date_is_not_valid'),
    'alpha' => ':attribute ' . __('lang.must_only_contain_letters'),
    'alpha_dash' => ':attribute ' . __('lang.must_only_contain_letters_numbers_dashes'),
    'alpha_num' => ':attribute ' . __('lang.must_only_contain_letters_numbers'),
    'array' => ':attribute ' . __('lang.is_invalid'),
    'before' => ':attribute ' . __('lang.date_is_not_valid'),
    'before_or_equal' => ':attribute ' . __('lang.date_is_not_valid'),
    'between' => [
        'numeric' => ':attribute ' . __('lang.is_invalid'),
        'file' => ':attribute ' . __('lang.is_invalid'),
        'string' => ':attribute ' . __('lang.is_invalid'),
        'array' => ':attribute ' . __('lang.is_invalid'),
    ],
    'boolean' => ':attribute ' . __('lang.is_invalid'),
    'confirmed' => ':attribute ' . __('lang.confirmation_text_does_not_match'),
    'date' => ':attribute ' . __('lang.date_is_not_valid'),
    'date_format' => ':attribute ' . __('lang.date_is_not_valid'),
    'different' => ':attribute ' . __('lang.is_invalid'),
    'digits' => ':attribute ' . __('lang.must_only_contain_numbers'),
    'digits_between' => ':attribute ' . __('lang.is_invalid'),
    'dimensions' => ':attribute ' . __('lang.is_invalid'),
    'distinct' => ':attribute ' . __('lang.is_invalid'),
    'email' => ':attribute ' . __('lang.is_not_a_valid_email_address'),
    'exists' => ':attribute ' . __('lang.does_not_exist'),
    'gt' => [
        'numeric' => ':attribute ' . __('lang.must_be_greater_than') . ' :value',
        'file' => ':attribute ' . __('lang.must_be_greater_than') . ' :value',
        'string' => ':attribute ' . __('lang.must_be_greater_than') . ' :value',
        'array' => ':attribute ' . __('lang.must_be_greater_than') . ' :value',
    ],
    'gte' => [
        'numeric' => ':attribute ' . __('lang.must_be_greater_than_or_equal_to') . ' :value',
        'file' => ':attribute ' . __('lang.must_be_greater_than_or_equal_to') . ' :value',
        'string' => ':attribute ' . __('lang.must_be_greater_than_or_equal_to') . ' :value',
        'array' => ':attribute ' . __('lang.must_be_greater_than_or_equal_to') . ' :value',
    ],
    'file' => ':attribute ' . __('lang.is_not_a_valid_file'),
    'filled' => ':attribute ' . __('lang.must_not_be_blank'),
    'image' => ':attribute ' . __('lang.is_not_a_valid_image'),
    'in' => ':attribute ' . __('lang.is_invalid'),
    'in_array' => ':attribute ' . __('lang.is_invalid'),
    'integer' => ':attribute ' . __('lang.must_be_a_whole_nuber'),
    'ip' => ':attribute ' . __('lang.is_not_a_valid_ip_address'),
    'ipv4' => ':attribute ' . __('lang.is_not_a_valid_ip_address'),
    'ipv6' => ':attribute ' . __('lang.is_not_a_valid_ip_address'),
    'json' => ':attribute ' . __('lang.is_invalid'),
    'max' => [
        'numeric' => ':attribute ' . __('lang.must_be_a_number_not_greater_than') . ' :max',
        'file' => ':attribute ' . __('lang.is_invalid'),
        'string' => ':attribute ' . __('lang.is_invalid'),
        'array' => ':attribute ' . __('lang.is_invalid'),
    ],
    'mimes' => ':attribute ' . __('lang.is_invalid'),
    'mimetypes' => ':attribute ' . __('lang.is_invalid'),
    'min' => [
        'numeric' => ':attribute ' . __('lang.must_be_a_number_greater_than') . ' :min',
        'file' => ':attribute ' . __('lang.is_invalid'),
        'string' => ':attribute ' . __('lang.is_invalid'),
        'array' => ':attribute ' . __('lang.is_invalid'),
    ],
    'not_in' => ':attribute ' . __('lang.is_invalid'),
    'numeric' => ':attribute ' . __('lang.is_not_a_valid_number'),
    'present' => ':attribute field must be present.',
    'regex' => ':attribute ' . __('lang.is_invalid'),
    'required' => ':attribute ' . __('lang.is_required'),
    'required_if' => ':attribute ' . __('lang.is_required'),
    'required_unless' => ':attribute ' . __('lang.is_required'),
    'required_with' => ':attribute ' . __('lang.is_required'),
    'required_with_all' => ':attribute ' . __('lang.is_required'),
    'required_without' => ':attribute ' . __('lang.is_required'),
    'required_without_all' => ':attribute ' . __('lang.is_required'),
    'same' => ':attribute ' . __('lang.values_do_no_match'),
    'size' => [
        'numeric' => ':attribute must be :size.',
        'file' => ':attribute must be :size kilobytes.',
        'string' => ':attribute must be :size characters.',
        'array' => ':attribute must contain :size items.',
    ],
    'string' => ':attribute ' . __('lang.must_only_contain_letters_numbers'),
    'timezone' => ':attribute must be a valid zone.',
    'unique' => ':attribute ' . __('lang.is_already_taken'),
    'uploaded' => ':attribute ' . __('lang.upload_failed'),
    'url' => ':attribute ' . __('lang.is_not_a_valid_url'),

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
     */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    | These lines are used to swap attribute (form field name) place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email".
    |
     */
    'attributes' => [

        //common fields
        'role_id' => strtolower(__('lang.user_role')),
        'first_name' => strtolower(__('lang.first_name')),
        'last_name' => strtolower(__('lang.last_name')),
        'email' => strtolower(__('lang.email_address')),
        'password' => strtolower(__('lang.password')),
        'password_confirmation' => strtolower(__('lang.password_confirmation')),
        'city' => strtolower(__('lang.city')),
        'country' => strtolower(__('lang.country')),
        'phone' => strtolower(__('lang.phone')),
        'day' => strtolower(__('lang.day')),
        'month' => strtolower(__('lang.month')),
        'year' => strtolower(__('lang.year')),
        'hour' => strtolower(__('lang.hour')),
        'minute' => strtolower(__('lang.minute')),
        'second' => strtolower(__('lang.second')),
        'title' => strtolower(__('lang.title')),
        'date' => strtolower(__('lang.date')),
        'time' => strtolower(__('lang.time')),
        'recaptcha' => strtolower(__('lang.recaptcha')),
        'subject' => strtolower(__('lang.subject')),
        'message' => strtolower(__('lang.message')),

        //create or edit projects form
        'project_title' => strtolower(__('lang.project_title')),
        'project_date_start' => strtolower(__('lang.start_date')),
        'project_categoryid' => strtolower(__('lang.category')),
        'project_title' => strtolower(__('lang.project_title')),
        'clientperm_tasks' => strtolower(__('lang.project_title')),
        'project_clientid' => strtolower(__('lang.client')),

        //create or edit clients form
        'client_company_name' => strtolower(__('lang.company_name')),

        //create or edit leads
        'lead_title' => strtolower(__('lang.title')),
        'lead_firstname' => strtolower(__('lang.first_name')),
        'lead_lastname' => strtolower(__('lang.last_name')),
        'lead_email' => strtolower(__('lang.email_address')),
        'lead_company_name' => strtolower(__('lang.company_name')),
        'lead_job_position' => strtolower(__('lang.job_title')),
        'lead_street' => strtolower(__('lang.street')),
        'lead_city' => strtolower(__('lang.city')),
        'lead_state' => strtolower(__('lang.state')),
        'lead_zip' => strtolower(__('lang.zipcode')),
        'lead_country' => strtolower(__('lang.country')),
        'lead_website' => strtolower(__('lang.website')),


        //create or edit task
        'task_projectid' => strtolower(__('lang.project')),
        'task_title' => strtolower(__('lang.title')),
        'task_status' => strtolower(__('lang.status')),
        'task_priority' => strtolower(__('lang.priority')),

        //create or edit invoice or clone
        'invoice_date' => strtolower(__('lang.invoice_date')),
        'bill_due_date' => strtolower(__('lang.due_date')),
        'bill_clientid' => strtolower(__('lang.client')),
        'bill_projectid' => strtolower(__('lang.project')),
        'bill_categoryid' => strtolower(__('lang.category')),
        'bill_recurring' => strtolower(__('lang.recurring')),
        'bill_recurring_duration' => strtolower(__('lang.duration')),
        'bill_recurring_period' => strtolower(__('lang.period')),
        'bill_recurring_cycles' => strtolower(__('lang.cycles')),
        'bill_recurring_next' => strtolower(__('lang.start_date')),
        'bill_due_date' => strtolower(__('lang.due_date')),

        'js_item_unit' => strtolower(__('lang.unit')),
        'bill_amount_before_tax' => strtolower(__('lang.amount_before_tax')),
        'bill_final_amount' => strtolower(__('lang.final_amount')),
        'bill_tax_total_amount' => strtolower(__('lang.tax_amount')),
        'bill_discount_percentage' => strtolower(__('lang.discount')),
        'bill_subtotal' => strtolower(__('lang.subtotal')),
        'bill_amount_before_tax' => strtolower(__('lang.amount_before_tax')),
        'bill_tax_total_percentage' => strtolower(__('lang.tax')),
        'bill_discount_percentage' => strtolower(__('lang.discount')),
        'bill_discount_amount' => strtolower(__('lang.discount')),


        //create or edit estimates
        'bill_date' => strtolower(__('lang.date')),
        'bill_expiry_date' => strtolower(__('lang.expiry_date')),
        'bill_categoryid' => strtolower(__('lang.category')),
        'bill_clientid' => strtolower(__('lang.client')),

        //items
        'item_description' => strtolower(__('lang.description')),
        'item_unit' => strtolower(__('lang.unit')),
        'item_rate' => strtolower(__('lang.rate')),
        'item_categoryid' => strtolower(__('lang.category')),

        //note
        'note_title' => strtolower(__('lang.title')),
        'note_description' => strtolower(__('lang.description')),

        //payment
        'payment_gateway' => strtolower(__('lang.payment_method')),
        'payment_date' => strtolower(__('lang.payment_date')),
        'payment_amount' => strtolower(__('lang.amount')),
        'payment_invoiceid' => strtolower(__('lang.invoice_id')),

        //settings - general
        'settings_company_name' => strtolower(__('lang.company_name')),
        'settings_system_date_format' => strtolower(__('lang.date_format')),
        'settings_system_datepicker_format' => strtolower(__('lang.date_picker_format')),
        'settings_system_default_leftmenu' => strtolower(__('lang.main_menu_default_state')),
        'settings_system_default_statspanel' => strtolower(__('lang.stats_panel_default_state')),
        'settings_system_pagination_limits' => strtolower(__('lang.table_pagination_limits')),
        'settings_system_kanban_pagination_limits' => strtolower(__('lang.kanban_pagination_limits')),
        'settings_system_currency_symbol' => strtolower(__('lang.currency_symbol')),
        'settings_system_currency_position' => strtolower(__('lang.currency_symbol_position')),
        'settings_system_close_modals_body_click' => strtolower(__('lang.modal_window_close_on_body_click')),

        'settings_company_address_line_1' => strtolower(__('lang.address')),
        'settings_company_city' => strtolower(__('lang.city')),
        'settings_company_state' => strtolower(__('lang.state')),
        'settings_company_zipcode' => strtolower(__('lang.zipcode')),
        'settings_company_country' => strtolower(__('lang.country')),
        'settings_company_telephone' => strtolower(__('lang.telephone')),

        'settings_projects_default_hourly_rate' => strtolower(__('lang.rate')),
        'settings_projects_assignedperm_tasks_collaborate' => strtolower(__('lang.permissions')),
        'settings_projects_clientperm_tasks_view' => strtolower(__('lang.permissions')),
        'settings_projects_clientperm_tasks_collaborate' => strtolower(__('lang.permissions')),
        'settings_projects_clientperm_tasks_create' => strtolower(__('lang.permissions')),
        'settings_projects_clientperm_timesheets_view' => strtolower(__('lang.permissions')),
        'settings_projects_clientperm_expenses_view' => strtolower(__('lang.permissions')),

        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
        'foo' => strtolower(__('lang.bar')),
               

        //settings - categories
        'category_name' => strtolower(__('lang.name')),

        //settig - tags
        'tag_title' => strtolower(__('lang.tag_title')),

        //setting - lead sources
        'leadsources_title' => strtolower(__('lang.source_name')),

        //setting - lead sources
        'leadstatus_title' => strtolower(__('lang.status_name')),

        //setting - theme
        'settings_theme_name' => strtolower(__('lang.theme')),

        //setting - email
        'settings_email_from_address' => strtolower(__('lang.system_email_address')),
        'settings_email_from_name' => strtolower(__('lang.system_from_name')),
        'settings_email_smtp_host' => strtolower(__('lang.smtp_host')),
        'settings_email_smtp_port' => strtolower(__('lang.smtp_port')),
        'settings_email_smtp_username' => strtolower(__('lang.username')),
        'settings_email_smtp_password' => strtolower(__('lang.password')),

        //settings invoices
        'settings_invoices_recurring_grace_period' => strtolower(__('lang.bill_recurring_grace_period')),

        //milestone
        'milestone_title' => strtolower(__('lang.milestone_name')),

        //knowledgebase
        'category_visibility' => strtolower(__('lang.visible_to')),
        'knowledgebase_title' => strtolower(__('lang.title')),
        'knowledgebase_text' => strtolower(__('lang.description')),
        'kbcategory_title' => strtolower(__('lang.category_name')),

        //expenses
        'expense_description' => strtolower(__('lang.description')),
        'expense_clientid' => strtolower(__('lang.client')),
        'expense_projectid' => strtolower(__('lang.project')),
        'expense_date' => strtolower(__('lang.date')),
        'expense_amount' => strtolower(__('lang.amount')),
        'expense_categoryid' => strtolower(__('lang.category')),
        'expense_billable' => strtolower(__('lang.bar')),

        //tickets
        'ticket_clientid' => strtolower(__('lang.client')),
        'ticket_categoryid' => strtolower(__('lang.department')),
        'ticket_projectid' => strtolower(__('lang.project')),
        'ticket_subject' => strtolower(__('lang.subject')),
        'ticket_message' => strtolower(__('lang.message')),
        'ticketreply_text' => strtolower(__('lang.message')),
        'ticketreply_ticketid' => strtolower(__('lang.ticket')),

        //comment
        'comment_text' => strtolower(__('lang.comment')),

        //email templates
        'emailtemplate_body' => strtolower(__('lang.email_body')),
        'emailtemplate_subject' => strtolower(__('lang.email_subject')),

        //settings - payment methods
        'settings_paypal_currency' => strtolower(__('lang.currency')),
        'settings_stripe_display_name' => strtolower(__('lang.display_name')),
        'settings_paypal_display_name' => strtolower(__('lang.display_name')),
        'settings_bank_display_name' => strtolower(__('lang.display_name')),

        //settings - milestone
        'milestonecategory_title' => strtolower(__('lang.milestone_name')),

        //cards
        'checklist_text' => strtolower(__('lang.checklist')),

    ],

];
