<?php
/**---------------------------------------------------------------------------------------------------------------
 * [END USER NOTES]
 * You can edit various setting in this file. Always save a backup copy, before making and changed to this file
 *----------------------------------------------------------------------------------------------------------------*/

/**----------------------------------------------------------------------------------------------------------------
 * [DEVELOPER NOTES]
 * Structural changes to this file (e.g. array keys) must be matched in the SanityChecks middleware
 * ---------------------------------------------------------------------------------------------------------------*/

return [

    /**
     * Various project statuses, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'project_statuses' => [
        'not_started' => 'default',
        'in_progress' => 'info',
        'on_hold' => 'warning',
        'cancelled' => 'purple',
        'completed' => 'success',
    ],

    /**
     * Various invoice statuses, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'invoice_statuses' => [
        'draft' => 'default',
        'due' => 'warning',
        'overdue' => 'danger',
        'paid' => 'success',
        'part_paid' => 'info',
    ],

    /**
     * Various estimate statuses, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'estimate_statuses' => [
        'draft' => 'default',
        'new' => 'info',
        'accepted' => 'success',
        'declined' => 'warning',
        'revised' => 'primary',
        'expired' => 'danger',
    ],

    /**
     * Various task statuses, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'task_statuses' => [
        'new' => 'default',
        'in_progress' => 'info',
        'testing' => 'purple',
        'awaiting_feedback' => 'warning',
        'completed' => 'success',
    ],

    /**
     * Various task priority, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     * normal | high | urgent
     */
    'task_priority' => [
        'low' => 'default',
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ],

    /**
     * Various ticket statuses, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'ticket_statuses' => [
        'open' => 'info',
        'on_hold' => 'warning',
        'answered' => 'success',
        'closed' => 'default',
    ],

    /**
     * Various ticket priority, with their corresponding bootstrap colors (colors are used for labels etc)
     * The following colors are available: (default|info|warning|success|danger|primary|green|lime|brown)
     * [IMPORTANT WARNING]
     * Only change the color values
     */
    'ticket_priority' => [
        'normal' => 'info',
        'high' => 'warning',
        'urgent' => 'danger',
    ],

    /**
     * This determins the type of events that are notified to users
     *    i.e. - events that show up in the notifications tab and that are sent as email notifications
     * [options]
     *  true | false
     *
     */
    'notifiable_events' => [

        //all user notifications

        //team only notifications
        'event_team_new_assignment' => true,
        'event_team_new_payment' => true,
        'event_team_project_status_change' => true,
        'event_team_lead_status_change' => true,
        'event_team_task_status_change' => true,

        //client only notifications
        'event_client_new_invoice' => true,
        'event_client_ticket_reply' => true,
        'event_client_project_status_change' => true,

    ],

    /**
     * Optionally show placeholder [disabled] actions buttons (e.g. delete/edit buttons etc), when the user does not have required permissions
     * If disabled, the buttons will simply not be viabled at all
     * [options]
     *  true | false
     */
    'placeholder_actions_buttons' => true,

    /**
     * Show small information tooltips when user hovers over Edit, Delete, etc buttons
     * [options]
     *  true | false
     */
    'show_action_button_tooltips' => true,

    /**
     * used as auto increment in db position.
     * You should not usually need to change this value
     */
    'db_position_increment' => 16384,

    /**
     * file uploads and attachments
     * Array of file types (by file extension) that users are not allowed to upload.
     * You can add to this array, using the exampe format below
     * [example]
     *
     *  'disallowed_file_types' => [
     *               '.php',
     *               '.exe',
     *               '.mp3',
     *   ],
     */
    'disallowed_file_types' => [
        '.php',
        '.exe',
    ],

    /**
     * Pagination limits of other specific items.... (which are not managed via the admin dashboard)
     * [notes]
     *  - 'pagination_project_timeline' - as displayed on the project home page (right panel)
     *  - 'pagination_event_notifications' - as displayed in top navigation, notifications drop down
     */
    'limits' => [
        'pagination_project_timeline' => 12,
        'pagination_event_notifications' => 200,
    ],

    /**
     * This set the default notification settings for a new client user/contact
     * [available option]
     *   - 'no'         : user will not receive any in-app notifications and will not receive any email notificatons
     *   - 'yes'        : user will only recieve in-app notifications
     *   - 'yes_email'  : user will recieve both in-app notifications and email notifications
     */
    'default_notifications_client' => [
        'notifications_new_project' => 'yes_email',
        'notifications_projects_activity' => 'yes_email',
        'notifications_billing_activity' => 'yes_email',
        'notifications_tasks_activity' => 'yes_email',
        'notifications_tickets_activity' => 'yes_email',
        'notifications_system' => 'yes_email',
    ],

    /**
     * This set the default notification settings for a new team member
     * [available option]
     *   - 'no'         : user will not receive any in-app notifications and will not receive any email notificatons
     *   - 'yes'        : user will only recieve in-app notifications
     *   - 'yes_email'  : user will recieve both in-app notifications and email notifications
     */
    'default_notifications_team' => [
        'notifications_projects_activity' => 'yes_email',
        'notifications_billing_activity' => 'yes_email',
        'notifications_new_assignement' => 'yes_email',
        'notifications_leads_activity' => 'yes_email',
        'notifications_tasks_activity' => 'yes_email',
        'notifications_tickets_activity' => 'yes_email',
        'notifications_system' => 'yes_email',
    ],

    /**
     * Force all nearly created users/contacts/team to change their system generated password when they first login
     * [available option]
     *   - 'no'
     *   - 'yes'
     */
    'force_password_change' => 'yes',

    /**
     * Projects - Default results table sorting
     *
     *   [sort_by] (available options)
     *    - project_id
     *    - project_title
     *    - project_date_start
     *    - project_date_due
     *    - project_status
     *    - project_progress
     *    - client_company_name
     *
     *    [sort_by] (ascending or descending)
     *    - ASC
     *    - DESC
     *
     */
    'ordering_projects' => [
        'sort_by' => 'project_id',
        'sort_order' => 'DESC',
    ],

    /**
     * Invoices - Default results table sorting
     *
     *   [sort_by] (available options)
     *    - bill_invoiceid
     *    - bill_date
     *    - bill_due_date
     *    - bill_final_amount
     *    - bill_status
     *    - client_company_name
     *
     *    [sort_by] (ascending or descending)
     *    - ASC
     *    - DESC
     *
     */
    'ordering_invoices' => [
        'sort_by' => 'bill_invoiceid',
        'sort_order' => 'DESC',
    ],

    /**
     * Estimates - Default results table sorting
     *
     *   [sort_by] (available options)
     *    - bill_estimateid
     *    - bill_date
     *    - bill_expiry_date
     *    - bill_final_amount
     *    - bill_status
     *    - client_company_name
     *
     *    [sort_by] (ascending or descending)
     *    - ASC
     *    - DESC
     *
     */
    'ordering_estimates' => [
        'sort_by' => 'bill_estimateid',
        'sort_order' => 'DESC',
    ],

    /**
     * Expenses - Default results table sorting
     *
     *   [sort_by] (available options)
     *    - expense_id
     *    - expense_date
     *    - expense_amount
     *    - client_company_name
     *    [sort_by] (ascending or descending)
     *    - ASC
     *    - DESC
     *
     */
    'ordering_expenses' => [
        'sort_by' => 'expense_id',
        'sort_order' => 'DESC',
    ],

    /**
     * Payments - Default results table sorting
     *
     *   [sort_by] (available options)
     *    - payment_id
     *    - payment_date
     *    - payment_invoiceid
     *    - payment_amount
     *
     *    [sort_by] (ascending or descending)
     *    - ASC
     *    - DESC
     *
     */
    'ordering_payments' => [
        'sort_by' => 'payment_id',
        'sort_order' => 'DESC',
    ],

    /**
     * Popup notification position
     * [available option]
     *   - 'topLeft'
     *   - 'topRight'
     *   - 'topCenter'
     *   - 'bottomLeft'
     *   - 'bottomRight'
     *   - 'bottomCenter'
     */
    'notification_position' => 'bottomLeft',

    /**
     * [error] Popup notification visibile duration (in millisecond)
     * e.g. 2000 = 2 seconds
     */
    'notification_error_duration' => 5000,

    /**
     * [success] Popup notification visibile duration (in millisecond)
     * e.g. 2000 = 2 seconds
     */
    'notification_success_duration' => 3000,

    /** ---------------------------------------------------POST RELEASE CHANGES-------------------------------------------------------- /
     * Optional new settings. If these settings do not exist in a previous version of Grow CRM, a user can simply add them
     * These are none breaking settings
     * [NEXTLOOP NOTES] - 
     * 
     *  (1) This settings file is automatically deleted from any update but is included in new releases
     *  (2) New settings added here can (optionally) be added to the middleware /general/settings, for fallback value for old versions
     *      this will allow for using the configs in the application as if they exist for all version of Grow CRM
     *      For clarity, any setting here which has a fallback in settings middleware, must be marked with [*]
     * ---------------------------------------------------------------------------------------------------------------------------------*/

    /**
     * Allow you to change the urls dynamicallay
     * NB: This require custom coding to be done in the route file and will no work out of the box
     *
     */
    'url_mapping' => [
        //'projects' => 'designs',
    ],

    /** 
     * [*]
     * how many rows to show in settings. Defaults to a hard set value, if not present
     */
    //'custom_fields_display_limit' => 5,

];