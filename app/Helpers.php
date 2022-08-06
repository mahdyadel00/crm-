<?php

/**
 * Uses php uiniqid
 * @return string a very random unique id
 */
function str_unique() {
    return uniqid('', true);
}

function random_string($length = 10) {
    return str_alphnumeric($length);
}

/**
 * generate random alphanumeric string
 * [example] U7gf5dD
 * @return string
 */
function str_alphnumeric($length = 10) {

    $string = '';

    // You can define your own characters here.
    $characters = "23457562948562749466789ABCDEFHJKLMNPRTVWXYZ23456789";

    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
}

/**
 * returns "selected" for dropdown list preselection
 * in the example below, if the database value is "active' this option will be "selected"
 * @example:
 *     <option value="active" {{ runtimePreselected('active', project['project_status']) }}>Active</option>
 *     <option value="suspended" {{ runtimePreselected('suspended', project['project_status']) }}>Suspended</option>
 * @param $option_value string [the hard coded value of the select option (e.g. <option="yes" ....)
 * @param $actual_value string [actual value i.e. from database (e.g. <option value="no"...)]
 * @return string hidden | visible
 */
function runtimePreselected($actual_value = 'bar', $option_value = 'foo') {
    if ($option_value == $actual_value) {
        return 'selected';
    }
    return;
}

/**
 * returns "checked="checked"" for radio fields
 * @return string hidden | visible
 */
function runtimePreChecked2($actual_value = 'bar', $option_value = 'foo') {
    if ($option_value == $actual_value) {
        return 'checked';
    }
    return;
}

/**
 * returns "selected" for dropdown list preselection
 * checks if provided value is in provided arrat
 * @example:
 *     <option value="active" {{ runtimePreselectedInArray(project['project_status'], 'active') }}>Active</option>
 * @param $value string actul value coing from database
 * @param $list array the haystack to check againt
 * @return string hidden | visible
 */
function runtimePreselectedInArray($value = '', $list = array()) {

    if (!is_array($list) || $value == '') {
        return;
    }

    //case  insensitive (unlike in_array)
    foreach ($list as $var) {
        if (strtolower($var) == strtolower($value)) {
            return 'selected';
        }
    }

    return;
}

/**
 * returns "checked" for prechecked check boxes
 * @example:
 *     <input type="checkbox" id="foobar" name="foobar" class="filled-in chk-col-light-blue" {{ runtimePrechecked(project['project_status']) }}>
 * @param $actual_value string yes|no|checked|active|etc [this value comes from the database]
 * @return string hidden | visible
 */
function runtimePrechecked($option_value = '') {

    //keywords
    $checked = [
        'yes',
        'on',
        'active',
        'enabled',
        'checked',
        'selected',
        'billable',
        'global',
        'sandbox',
        'completed',
        'toggled', //customfields
        'show'];

    if (in_array($option_value, $checked)) {
        return 'checked';
    }
    return;
}

/**
 * returns "active" for css button etc
 * @param $actual_value string yes|no|active|etc [this value comes from the database]
 * @return string hidden | visible
 */
function runtimeActive($option_value = '') {

    //keywords
    $checked = array('yes', 'active', 'enabled', 'kanban');
    if (in_array($option_value, $checked)) {
        return 'active';
    }

    return;
}

/**
 * check if string is empty. If its is, return a placeholder like '---'
 * @param string $var the string to be checked
 * @return string
 */
function runtimeCheckBlank($var = '') {
    if ($var == '' || $var == NULL) {
        return '---';
    }
    return $var;
}

/**
 * user preference on left menu (collapsed or open)
 * @param string $var current users setting
 * @return string css setting
 */
function runtimePreferenceLeftmenuPosition($var = '') {
    if ($var == 'collapsed') {
        return 'mini-sidebar';
    }
    return;
}

/**
 * user preference on left menu (collapsed or open)
 * @param string $var current users setting
 * @return string css setting
 */
function runtimePreferenceStatsPanelPosition($var = '') {
    if ($var == 'collapsed') {
        return 'hidden';
    }
    return;
}

/**
 * user preference on left menu (collapsed or open)
 * @param string $var current users setting
 * @return string css setting
 */
function runtimeMomentFormat($var = '') {

    switch ($var) {

    case 'd-m-Y':
        return 'DD-MM-YYYY';
        break;
    case 'd/m/Y':
        return 'DD/MM/YYYY';
        break;
    case 'm-d-Y':
        return 'MM-DD-YYYY';
        break;
    case 'm/d/Y':
        return 'MM/DD/YYYY';
        break;
    case 'Y-m-d':
        return 'YYYY-MM-DD';
        break;
    case 'Y/m/d':
        return 'YYYY/MM/DD';
        break;
    case 'Y-d-m':
        return 'YYYY-DD-MMMM';
        break;
    case 'Y/d/m':
        return 'YYYY/DD/MM';
        break;
    default:
        return 'MM-DD-YYYY';
        break;
    }
}

/**
 * Takes the current url and updates the page number to
 * to the specified one. If no page number existed in url
 * it will simply be added
 * @param string $name The name of the user
 * @param int $id The user id
 * @return bool
 */
function loadMoreButtonUrl($page = '', $type = '') {
    //get an array of all the current url queries
    $queries = request()->query();
    //update/add page number
    $queries['page'] = $page;
    $queries['source'] = $type;
    $queries['action'] = 'load';

    //return a full url with updated value
    $url = request()->fullUrlWithQuery($queries);

    //remove unwanted (system_languages%5B2%5D=afrikaans) etc from the url. These are coming from the languages dropdown
    $url = preg_replace('/&system_languages%5B[\d]+%5D=[\w]+/', '', $url);
    $url = preg_replace('/&visibility_left_menu_toggle_button=[\w]+/', '', $url);
    $url = preg_replace('/&system_language=[\w]+/', '', $url);
    $url = preg_replace('/&user_has_due_reminder=[\w]+/', '', $url);
    $url = preg_replace('/&toggle=[\w]+/', '', $url);

    //return url
    return $url;
}

/**
 * takes url coming from sorting menu links and flips
 * the current sorting to opposite, for next time
 * @param string $url the current url
 * @param string $current_sorting the current sorting
 * @return bool
 */
function flipSortingUrl($url = '', $current_sorting = '') {

    //no sorting found in url
    if (!in_array($current_sorting, array('desc', 'asc'))) {
        return $url;
    }

    //flip it
    if ($current_sorting == 'desc') {
        return str_replace('desc', 'asc', $url);
    } else {
        return str_replace('asc', 'desc', $url);
    }

}

/**
 * get the users avatar. if it does not exist return the default avatar
 * @return string
 */
function getUsersAvatar($directory = '', $filename = '', $user_id = '') {

    //dd($user_id);

    if ($user_id === 0) {
        return url('storage/avatars/system/avatar.jpg');
    }

    //from database
    $avatar = "/avatars/$directory/$filename";
    //check if exists
    if (Storage::exists($avatar) && $filename != '' && $directory != '') {
        return url(Storage::url($avatar));
    }
    //default avatar
    return url('storage/avatars/system/default_avatar.jpg');
}

/**
 * used to return the 'system' users name, based on user_id = 0
 * @return string
 */
function checkUsersName($first_name = '', $user_id = '') {

    if ($user_id === 0) {
        return __('lang.system_bot_name');
    }

    //return actual user's first name
    return $first_name;
}

/**
 * bootstrap label class, based on status
 * @return string bootstrap label class
 */
function runtimeClientStatusLabel($status = '') {
    switch ($status) {
    case 'active':
        return 'label-outline-info';
        break;
    case 'suspended':
        return 'label-outline-warning';
        break;
    }
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeProjectStatusColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.project_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeExpenseStatusColors($value = '', $type = 'label') {

    //get the settings value
    switch ($value) {
    case 'billable':
        $colour = 'info';
        break;
    case 'not_billabled':
        $colour = 'default';
        break;
    case 'invoiced':
        $colour = 'success';
        break;
    case 'not_invoiced':
        $colour = 'default';
        break;
    default:
        $colour = 'default';
        break;
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeInvoiceStatusColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.invoice_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeEstimateStatusColors($value = '', $type = 'label') {

    //get the css value from config
    foreach (config('settings.estimate_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeTaskStatusColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.task_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeLeadStatusColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('system.lead_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeTaskPriorityColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.task_priority') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the ticket
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeTicketStatusColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.ticket_statuses') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the ticket
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeTicketPriorityColors($value = '', $type = 'label') {

    //default colour
    $colour = 'default';

    //get the css value from config
    foreach (config('settings.ticket_priority') as $key => $css) {
        if ($value == $key) {
            $colour = $css;
        }
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string $status the status of the ticket
 * @param string $type lable|background
 * @return string bootstrap label class
 */
function runtimeSubscriptionsColors($status = '', $type = 'label') {

    //default colour
    $colour = 'default';

    switch ($status) {

    case 'pending':
        $colour = 'default';
        break;

    case 'active':
        $colour = 'success';
        break;

    case 'failed':
        $colour = 'warning';
        break;

    case 'cancelled':
        $colour = 'danger';
        break;
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * used by runtime functions to return the css
 * @param string value the status of the task
 * @param string type lable|background
 * @return string
 */
function bootstrapColors($colour = '', $type = '') {

    switch ($colour) {
    case 'default':
        if ($type == 'label') {
            return 'label-outline-default';
        }
        if ($type == 'background') {
            return 'bg-default';
        }
        if ($type == 'text') {
            return 'text-default';
        }
        break;
    case 'info':
        if ($type == 'label') {
            return 'label-outline-info';
        }
        if ($type == 'background') {
            return 'bg-info';
        }
        if ($type == 'text') {
            return 'text-info';
        }
        break;
    case 'success':
        if ($type == 'label') {
            return 'label-outline-success';
        }
        if ($type == 'background') {
            return 'bg-success';
        }
        if ($type == 'text') {
            return 'text-success';
        }
        break;

    case 'warning':
        if ($type == 'label') {
            return 'label-outline-warning';
        }
        if ($type == 'background') {
            return 'bg-warning';
        }
        if ($type == 'text') {
            return 'text-warning';
        }
        break;
    case 'danger':
        if ($type == 'label') {
            return 'label-outline-danger';
        }
        if ($type == 'background') {
            return 'bg-danger';
        }
        if ($type == 'text') {
            return 'text-danger';
        }
        break;
    case 'megna':
        if ($type == 'label') {
            return 'label-outline-megna';
        }
        if ($type == 'background') {
            return 'bg-megna';
        }
        if ($type == 'text') {
            return 'text-megna';
        }
        break;
    case 'purple':
        if ($type == 'label') {
            return 'label-outline-purple';
        }
        if ($type == 'background') {
            return 'bg-purple';
        }
        if ($type == 'text') {
            return 'text-purple';
        }
        break;
    case 'green':
        if ($type == 'label') {
            return 'label-outline-green';
        }
        if ($type == 'background') {
            return 'bg-green';
        }
        if ($type == 'text') {
            return 'text-green';
        }
        break;
    case 'lime':
        if ($type == 'label') {
            return 'label-outline-lime';
        }
        if ($type == 'background') {
            return 'bg-lime';
        }
        if ($type == 'text') {
            return 'text-lime';
        }
        break;
    case 'brown':
        if ($type == 'label') {
            return 'label-outline-brown';
        }
        if ($type == 'background') {
            return 'bg-brown';
        }
        if ($type == 'text') {
            return 'text-brown';
        }
        break;
    case 'primary':
        if ($type == 'label') {
            return 'label-outline-purple';
        }
        if ($type == 'background') {
            return 'bg-purple';
        }
        if ($type == 'text') {
            return 'text-purple';
        }
        break;
    default:
        if ($type == 'label') {
            return 'label-outline-info';
        }
        if ($type == 'background') {
            return 'bg-info';
        }
        if ($type == 'text') {
            return 'text-info';
        }
        break;
    }
}

/**
 * used by runtime functions to return the css
 * @param string value the status of the task board
 * @param string type tasks|leads
 * @return string
 */
function runtimeKanbanBoardColors($status = '') {

    return 'border-' . $status;

}

/**
 * Apply correct language for values language coming from the database
 * e.g. project status etc
 * if no lang was found, return origianl text
 * @return string bootstrap label class
 */
function runtimeLang($lang = '') {
    $language = strtolower($lang);
    $language = str_replace(' ', '_', $lang);
    if (Lang::has("lang.$language")) {
        return __("lang.$language");
    } else {
        return str_replace('_', ' ', $lang);
    }
}

/**
 * language for modules
 * e.g. project status etc
 * if no lang was found, return origianl text
 * @return string bootstrap label class
 */
function runtimeLangModules($lang = '') {
    $language = strtolower($lang);
    $language = str_replace(' ', '_', $lang);
    try {
        return __($lang);
    } catch (Exception $e) {
        return str_replace('_', ' ', $lang);
    }
}

/**
 * change to [app/systen] lang and translate supplied string
 * afterwards, change back to [user] land
 * @return string bootstrap label class
 */
function runtimeSystemLang($lang = '') {

    //[UPCOMING] - set system language

    $language = strtolower($lang);
    if (Lang::has("lang.$language")) {
        return __("lang.$language");
    } else {
        return str_replace('_', ' ', $lang);
    }
}

/**
 * Format the date accoring to the system setting
 * @return string bootstrap label class
 */
function runtimeDate($date = '', $alternative = '---') {

    if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00' || $date == '---') {
        return $alternative;
    }

    if ($date != '') {
        $date_format = config('system.settings_system_date_format');
        return \Carbon\Carbon::parse($date)->format($date_format);
    }

    return $alternative;
}

/**
 * Format the time accoring to the system setting
 * @return string bootstrap label class
 */
function runtimeTime($date = '') {

    if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '---';
    }

    if ($date != '') {
        return \Carbon\Carbon::parse($date)->format('H:i');
    }

    return '---';
}

/**
 * Format the datepicker date accoring to the system (settings_system_datepicker_format)
 * @return string bootstrap label class
 */
function runtimeDatepickerDate($date = '') {
    if ($date != '') {
        $date_format = (config('system.settings_system_datepicker_format') == 'mm-dd-yyyy') ? 'm-d-Y' : 'd-m-Y';
        return \Carbon\Carbon::parse($date)->format($date_format);
    }
    return;
}

/**
 * Format the date accoring to the system setting
 * @return string bootstrap label class
 */
function runtimeDateAgo($date = '') {

    if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
        return '---';
    }

    if ($date != '') {
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }

    return '---';
}

/**
 * Check if select2 should allow users own tags
 * @return string select2 css setting for allowing tags or null
 */
function runtimeAllowUserTags() {
    if (config('system.settings_tags_allow_users_create') == 'yes') {
        return 'select2-tags';
    } else {
        return 'select2-basic';
    }
}

/**
 * Check a custom leads source is the one selected in the database
 * return a standard html <option> for that sources, preselected
 * @return string select2 css setting for allowing tags or null
 */
function runtimeLeadSourceCustom($list = array(), $value = '') {
    //validate
    if (!is_array($list) || $value == '') {
        return '';
    }
    //check if is in array (case insensitive)
    if (!in_array(strtolower($value), array_map('strtolower', $list))) {
        return '<option value="' . $value . '" selected>' . $value . '</option>';
    }
}

/**
 * convert seconds to human readable 00:00:00
 * @return string human readable time
 */
function runtimeSecondsHumanReadable($seconds = 0, $show_seconds = true) {

    $second = '<span class="timer-deliminator">:</span>';

    if (!is_numeric($seconds)) {
        return ($show_seconds) ? '00' . $second . '00' . $second . '00' : '00' . $second . '00';
    }

    $H = floor($seconds / 3600);
    $i = ($seconds / 60) % 60;
    $s = $seconds % 60;
    if ($show_seconds) {
        return sprintf("%02d" . $second . "%02d" . $second . "%02d", $H, $i, $s);
    } else {
        return sprintf("%02d" . $second . "%02d", $H, $i);
    }
}

/**
 * convert seconds to whole hours
 * @return string human readable time
 */
function runtimeSecondsWholeHours($seconds = 0) {

    //sanity
    if (!is_numeric($seconds)) {
        $seconds = 0;
    }

    //get whole hours
    $hours = floor($seconds / 3600);
    return $hours;
}

/**
 * convert whole minutes (after removing whole hours)
 * @return string human readable time
 */
function runtimeSecondsWholeMinutes($seconds = 0) {

    //sanity
    if (!is_numeric($seconds)) {
        $seconds = 0;
    }

    $minutes = floor(($seconds / 60) % 60);
    return $minutes;
}

/**
 * checks what the admin default setting are for:
 *   - task_client_visibility
 *   - task_billable
 *  used to check/uncheck checkboxes in the create task modal
 * @param string check
 * @return string checked | null
 */
function runtimeTasksDefaults($check = '') {

    //tasks visible to clients
    if ($check == 'task_client_visibility') {
        return (config('system.settings_tasks_client_visibility') == 'visible') ? 'checked' : '';
    }
    //tasks visible to clients
    if ($check == 'task_billable') {
        return (config('system.settings_tasks_billable') == 'billable') ? 'checked' : '';
    }

    return;
}

/**
 * return formatted invoice id (e.g. INV000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeInvoiceIdFormat($bill_invoiceid = '') {
    //add the zero's
    $prefix = config('system.settings_invoices_prefix');
    //return
    if (is_numeric($bill_invoiceid)) {
        return $prefix . str_pad($bill_invoiceid, 6, '0', STR_PAD_LEFT);
    } else {
        return '---';
    }
}

/**
 * return formatted estimate id  (e.g. EST000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeEstimateIdFormat($bill_estimateid = '') {
    //add the zero's
    $prefix = config('system.settings_estimates_prefix');
    //return
    if (is_numeric($bill_estimateid)) {
        return $prefix . str_pad($bill_estimateid, 6, '0', STR_PAD_LEFT);
    } else {
        return '---';
    }
}

/**
 * return formatted subscription id (e.g. SUB-000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeSubscriptionIdFormat($subscription_id = '') {
    //add the zero's
    $prefix = config('system.settings_subscriptions_prefix');
    //return
    if (is_numeric($subscription_id)) {
        return $prefix . str_pad($subscription_id, 6, '0', STR_PAD_LEFT);
    } else {
        return '---';
    }
}

/**
 * return human readabe file size e.g. 10MB
 * @param numeric file size in bytes
 * @return string checked | null
 */
function humanFileSize($bytes) {
    if ($bytes == 0) {
        return "0.00 B";
    }

    $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $e = floor(log($bytes, 1024));

    return round($bytes / pow(1024, $e), 2) . $s[$e];
}

/**
 * takes a filename and creates a thumbnail name. For consistent use in app
 * e.g. 'Some File.jpg' > 'thumb_Some_File.jpg'
 * @param string original filename
 * @return string thumbnail name
 */
function generateThumbnailName($filename = '') {
    return 'thumb_' . strtolower(preg_replace('/[^a-zA-Z0-9.]+/', '_', $filename));
}

/**
 * whether or not to allow modals to close on body click
 * this setting s coming from the admin settings
 * @return string bootstrap data attibutes for disallowing modals to close on body click
 */
function runtimeAllowCloseModalOptions() {
    if (config('system.settings_system_close_modals_body_click') == 'yes') {
        return;
    }
    return 'data-keyboard="false" data-backdrop="static"';
}

/**
 * Calculates the lenght of the 'progress' bars shown on the project home page
 * for the varios invoices statuse
 * @param numeric $all_invoices the total invoices value
 * @param numeric $compare_invoices the value of the invoices being compared
 * @return string html style string
 */
function runtimeProjectInvoicesBars($all_invoices = '', $compare_invoices = '') {

    //invalid data
    if (!is_numeric($all_invoices) || !is_numeric($compare_invoices)) {
        return 'w-0 h-px-3';
    }

    //invalid data
    if ($compare_invoices > $all_invoices) {
        return 'w-0 h-px-3';
    }

    //convert to cents
    $all_invoices = $all_invoices * 100;
    $compare_invoices = $compare_invoices * 100;

    if ($all_invoices == 0) {
        $percentage = 0;
    } else {
        $percentage = round(($compare_invoices / $all_invoices) * 100);
    }

    return 'w-' . $percentage . ' h-px-3';

}

/**
 * Remove unwanted characters from a tag
 *
 * @return string cleaned tag
 */
function cleanTag($tag = '') {
    //add dashes
    $tag = str_replace(' ', '-', $tag);

    //change to lowercase
    $tag = strtolower($tag);

    //remove none alpha-numeric characters - not sure how good this will be with none latin languages
    //$tag = preg_replace('/[^a-zA-Z0-9.]+/', '', $tag);
    return $tag;
}

/**
 * json response for a success notification popup
 */
function success($message = '') {

    //is there a message
    $message = ($message != '') ? $message : __('lang.request_has_been_completed');

    //notice
    $jsondata['notification'] = array('type' => 'success', 'value' => $message);

    //response
    return $jsondata;
}

/**
 * json response for an error notification popup
 */
function eror($message = '') {

    //is there a message
    $message = ($message != '') ? $message : __('lang.request_could_not_be_completed');

    //notice
    $jsondata['notification'] = array('type' => 'error', 'value' => $message);

    //response
    return $jsondata;
}

/**
 * return a nice slug for the database
 */
function createSlug($id = '', $text = '') {

    //response
    return str_slug($id . '-' . $text);
}

/**
 * checks timers running status and returns css color to match
 * @return string hidden | visible
 */
function runtimeTimerStatus($running = false) {
    if ($running) {
        return 'btn-outline-danger';
    }
    return 'btn-outline-default';
}

/**
 * checks timers running status and returns correct tooltip
 * @return string hidden | visible
 */
function runtimeTimerTooltip($running = false) {
    if ($running) {
        return __('lang.stop_timer');
    }
    return __('lang.start_timer');
}

/**
 * disable 'billable' option if expense has already been invoiced
 * @return string
 */
function runtimeExpenseBillable($value = '') {
    if ($value == 'invoiced') {
        return 'disabled';
    }
}

/**
 * disable based on user permissions
 * @return string
 */
function runtimeChecklistCheckbox($value = '') {
    if ($value !== true) {
        return 'disabled';
    }
}

/**
 * return a formatted number (php number_format) (1,230.00)
 * @param string $number
 * @return string formatted number
 */
function runtimeNumberFormat($number = '') {

    //validation
    if (!is_numeric($number)) {
        $number = 0;
    }

    //decimal separator
    $decimal = runtimeCurrrencySeperators(config('system.settings_system_decimal_separator'));

    //thousand separator
    $thousands = runtimeCurrrencySeperators(config('system.settings_system_thousand_separator'));

    //format the number
    return number_format($number, 2, $decimal, $thousands);
}

/**
 * convert database symbol name into actual characters
 */
function runtimeCurrrencySeperators($value) {
    switch ($value) {
    case 'comma':
        $symbol = ",";
        break;
    case 'fullstop':
        $symbol = ".";
        break;
    case 'apostrophe':
        $symbol = "'";
        break;
    case 'space':
        $symbol = " ";
        break;
    default:
        $symbol = "";
        break;
    }
    return $symbol;
}

/**
 * convert bootstrap color codes to their css equivalent
 */
function runtimeColorCode($value) {
    switch ($value) {
    case 'default':
        $color = "#cccccc";
        break;
    case 'info':
        $color = "#20aee3";
        break;
    case 'success':
        $color = "#24d2b5";
        break;
    case 'danger':
        $color = "#ff5c6c";
        break;
    case 'warning':
        $color = "#ff9041";
        break;
    case 'primary':
        $color = "#6772e5";
        break;
    case 'lime':
        $color = "#cddc39";
        break;
    case 'brown':
        $color = "#795548";
        break;
    default:
        $color = "#cccccc";
        break;
    }
    return $color;
}

/**
 * return a formtted value with a currency symbol ($1,230.00)
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 * @return string css setting
 */
function runtimeMoneyFormat($number = '', $span_id = "") {

    $number = runtimeNumberFormat($number);

    //are we wrapping in a span
    if ($span_id != '') {
        $number = '<span id="' . $span_id . '">' . $number . '</span>';
    }

    return config('system.currency_symbol_left') . $number . config('system.currency_symbol_right');
}

/**
 * appends additional query string data to a url.
 * e.g. ?invoiceresource_type=project&?invoiceresource_id=28
 * Data is set via the [index] middleware
 * @param string $var current users setting
 * @return string css setting
 */
function urlResource($url = '') {

    if (request()->filled('resource_query')) {
        if (strpos($url, '?') !== false) {
            $url = $url . '&' . request('resource_query');
        } else {
            $url = $url . '?' . request('resource_query');
        }
    }

    //return complete ur;
    return url($url);
}

/**
 * allow for dynamic manipulation of hard coded urls
 * @param string $url url string from balde or response etc
 * @return string url parses by laravel url helper
 */
function _url($url = '') {

    /**
     * --------------------------------------------------------------------------------------------------
     * [REMAPPING URLS]
     *
     * remapping changes a hard coded url like
     *           <a href="{{ _url('/projects') }}"... becomes same as <a href="{{ _url('/moviess') }}"
     *
     * mapping comes from an array place inside settings.php (example below)
     *     'url_mapping' => [
     *         'projects' => 'movies',
     *      ],
     *
     * ---------------------------------------------------------------------------------------------------
     */
    if (config()->has('settings.url_mapping')) {
        $mapping = config('settings.url_mapping');
        if (is_array($mapping)) {
            foreach ($mapping as $key => $value) {
                if ($value != '') {
                    $url = str_replace($key, $value, $url);
                }
            }
        }
    }

    //process and return the url as normal
    return url($url);
}

/**
 * disabling contacts checkboxes
 * @param string $var indenty of the checkbox
 * @return string css
 */
function runtimeDisabledContactsChecboxes($var = '') {
    if ($var == 'yes') {
        return 'disabled';
    }
}

/**
 * disabling contacts checkboxes
 * @param string $var indenty of the checkbox
 * @return string css
 */
function runtimeDisabledTimesheetsCheckboxes($var = false) {
    if ($var) {
        return 'disabled';
    }
}

/**
 * disabling account owner checkbox
 * @param string $var indenty of the checkbox
 * @return string css
 */
function runtimeAccountOwnerDisabled($var = '') {
    if ($var == 'yes') {
        return 'disabled';
    }
}

/**
 * checking account owner checkbox
 * @param string $var indenty of the checkbox
 * @return string css
 */
function runtimeAccountOwnerCheckbox($var = '') {
    if ($var == 'yes') {
        return 'checked';
    }
}

/**
 * Optionally show placeholder [disabled] actions buttons (e.g. delete/edit buttons etc), when the user does not have required permissions
 * @return string css
 */
function runtimePlaceholdeActionsButtons() {
    if (!config('settings.placeholder_actions_buttons')) {
        return 'hidden';
    }
}

/**
 * returns fommated date for pre-filling date fields
 * formatted as dd-mm-yyyy | mm-dd-yyyy
 * @return string css
 */
function runtimeTodaysDate() {

    if (config('system.settings_system_datepicker_format') == 'dd-mm-yyyy') {
        return \Carbon\Carbon::now()->format('d-m-Y');
    } else {
        return \Carbon\Carbon::now()->format('m-d-Y');
    }
}

/**
 * returns fommated date for mysql database
 * formatted as yyyy-mm-dd
 * @return string css
 */
function runtimeTodaysDateMySQL() {

    return \Carbon\Carbon::now()->format('Y-m-d');

}

/**
 * converting various database values/statuses etc to lang
 * thisis useful when the database value is not human friendly
 * @param $value string database value
 * @param $field string the database column where the value is coming from
 * @return string css
 */
function runtimeDBlang($value = '', $field = '') {

    //task visibility
    if ($field == 'task_client_visibility') {
        switch ($value) {
        case 'yes':
            return runtimeLang('visible');
            break;
        case 'no':
            return runtimeLang('hidden');
            break;
        }
    }

    //task milestone title
    if ($field == 'task_milestone') {
        switch ($value) {
        case 'uncategorised':
            return runtimeLang('uncategorised');
            break;
        default:
            return $value;
            break;
        }
    }

}

/**
 * returns html activator (class | text | id) etc needed to enable editing/deleting a resource
 * @param string value normally coming from the database
 * @param string type e.g. edit-task-checklist
 * @return string html activator string
 */
function runtimePermissions($type = '', $value = '') {

    //get the settings value
    switch ($type) {

    //edit task or lead checklist
    case 'task-edit-checklist':
    case 'lead-edit-checklist':
        return ($value === true) ? 'js-card-checklist-toggle' : '';
        break;

    //edit task or lead description
    case 'lead-edit-title':
    case 'task-edit-title':
        return ($value === true) ? 'card-title-editable' : 'foo-bar';
        break;

    //assign users
    case 'task-assign-users':
        return ($value === true) ? 'js-card-settings-button-static' : '';
        break;
    }

}

/**
 * add 'hidden' class for timers that are stopped
 * @param string value normally coming from the database
 * @return string html activator string
 */
function runtimeTimerVisibility($value = '', $type = '') {

    if (($value === true || $value == 1)) {
        if ($type == 'running') {
            return 'visible-inline-block';
        }
    } else {
        if ($type == 'stopped') {
            return 'visible-inline-block';
        }
    }
}

/**
 * add 'running' class for timers that are running
 * @param string value normally coming from the database
 * @return string html activator string
 */
function runtimeTimerRunningStatus($value = '') {
    if ($value == 'running') {
        return 'timer-running';
    }
}

/**
 * check if user has a running timer and st visibility of clock icon
 * @param string value normally coming from the database
 * @return string css hidden|null
 */
function runtimeCardMyRunningTimer($value = '') {

    if ($value === true) {
        return '';
    } else {
        return 'hidden-forced';
    }
}

/**
 * show bell red icon if users has unread notifications
 * @param string value normally coming from the database
 * @return string css hidden|null
 */
function runtimeVisibilityNotificationIcon($count = 0) {
    if ($count === 0) {
        return 'hidden';
    }
    return '';
}

/**show if a template is for client or team
 * @return string css hidden|null
 */
function runtimeEmailTemplates($value = '') {
    if ($value == 'client') {
        return ' - (' . __('lang.client') . ')';
    }
    if ($value == 'team') {
        return ' - (' . __('lang.team') . ')';
    }
    if ($value == 'everyone') {
        return ' - (' . __('lang.all') . ')';
    }
    return '';
}

/**
 * display correct invoice status visibility (on invoice page)
 * @return string hidden|null
 */
function runtimeInvoiceStatus($lable = 'foo', $value = 'bar') {
    if ($lable == $value) {
        return '';
    }
    return 'hidden';
}

/**
 * display correct invoice status
 * @return string hidden|null
 */
function runtimeInvoiceAttachedProject($type = 'attached', $value = '') {
    if ($type == 'project-title' && !is_numeric($value)) {
        return 'hidden';
    }
    if ($type == 'alternative-title' && is_numeric($value)) {
        return 'hidden';
    }
}

/**
 * add css class 'hidden' to an element
 * @return string hidden|null
 */
function runtimeVisibility($type = 'invoice-recurring-icon', $value = '', $value2 = '') {

    //invoice recurring icon
    if ($type == 'invoice-recurring-icon') {
        return ($value == 'yes') ? '' : 'hidden';
    }

    //invoice actions menu - view child invoices
    if ($type == 'invoice-view-child-invoices') {
        return ($value == 'yes') ? '' : 'hidden';
    }

    //invoice actions menu - stop recurring
    if ($type == 'invoice-stop-recurring') {
        return ($value == 'yes') ? '' : 'hidden';
    }

    //invoice coluns - inline tax
    if ($type == 'invoice-column-inline-tax') {
        return ($value == 'inline') ? '' : 'hidden';
    }

    //attach/detttach invoice dropdown links
    if ($type == 'attach-invoice') {
        return (is_numeric($value)) ? 'hidden' : '';
    }
    if ($type == 'dettach-invoice') {
        return (is_numeric($value)) ? '' : 'hidden';
    }

    //topnav timer
    if ($type == 'topnav-timer') {
        return ($value == 'show') ? '' : 'hidden';
    }

    //edit client modal -module settings
    if ($type == 'client_app_modules_pemissions') {
        return ($value == 'custom') ? '' : 'hidden';
    }

    //proposal or invoce statuses
    if ($type == 'document-status') {
        return ($value == $value2) ? 'hidden' : '';
    }

}

/**
 * friend theme name, from the directory name
 * @return string
 */
function runtimeThemeName($name = '') {

    $ar = ['_', '-'];

    return ucwords(str_replace($ar, ' ', $name));

}

/**
 * display correct estimate status visibility (on estimate page)
 * @return string hidden|null
 */
function runtimeEstimateStatus($lable = 'foo', $value = 'bar') {
    if ($lable == $value) {
        return '';
    }
    return 'hidden';
}

/**
 * convert dollars to cents
 * @return numeric amount in cents
 */
function runtimeAmountInCents($amount = 0) {
    return $amount * 100;
}

/**
 * convert dollars to cents
 * @return numeric amount in cents
 */
function pretty_print($var = []) {
    echo "<pre>";
    print_r($var);
    echo "</pre>";
    die();
}

/**
 * return the url to logo
 * @return string
 */
function runtimeLogoSmall() {
    $logo = config('system.settings_system_logo_small_name');
    $version = config('system.settings_system_logo_versioning');
    return url("storage/logos/app/$logo?v=$version");
}

/**
 * return the url to logo
 * @return string
 */
function runtimeLogoLarge() {
    $logo = config('system.settings_system_logo_large_name');
    $version = config('system.settings_system_logo_versioning');
    return url("storage/logos/app/$logo?v=$version");
}

/**
 * set the bootstrap col-size for crumbs. If none is provided, set the default size col-lg-5
 * this was an afterthought, so some controllers are not setting this, hence the default size
 * @return string
 */
function runtimeCrumbsColumnSize($size = '') {
    if ($size != '') {
        return $size;
    } else {
        return 'col-lg-5';
    }
}

function runtimeSanitizeHTML($text = '') {

    $text = str_replace('<script>', '', $text);
    $text = str_replace('</script>', '', $text);
    return $text;
}

/**
 * clean a language string to remove html tags and trim shite spaces
 * @return string
 */

function cleanLang($str = '') {
    //remove double quotes
    $str = str_replace('"', '', $str);
    //trim html
    return trim(strip_tags($str));
}

/**
 * this is a trusted transactional email template, coming from the database
 * @return string
 */

function cleanEmail($text = '') {
    $text = str_replace('<script>', '', $text);
    $text = str_replace('</script>', '', $text);
    return $text;
}

/**
 * clean a string to remove html tags and trim shite spaces
 * @return string
 */

function safestr($str = '') {
    return trim($str);
}

/**
 * check if a string has HTML tags
 * @return bool
 */
function hasHTML($str = '') {
    if ($str != strip_tags($str)) {
        return true;
    }
    return false;
}

/**
 * return a name or string for unkownn/delete users
 * @return string
 */

function runtimeUnkownUser($str = '') {
    return __('lang.unknown');
}

/**
 * return some common paths
 * [EXAMPLE] /home/mydomain/publc_html
 * @return string
 */
function path_root($str = '') {
    return BASE_DIR;
}

/**
 * return some common paths
 * [EXAMPLE] /home/mydomain/publc_html/storage
 * @return string
 */
function path_storage($str = '') {
    return BASE_DIR . '/storage';
}

/**
 * return some common paths
 * [EXAMPLE] /home/mydomain/publc_html/storage/temp
 * @return string
 */
function path_temp($str = '') {
    return BASE_DIR . '/storage/temp';
}

/**
 * return some common paths
 * [EXAMPLE] /home/mydomain/publc_html/application
 * @return string
 */
function path_application($str = '') {
    return BASE_DIR . '/application';
}

/**
 * return some common paths
 * [EXAMPLE] /home/mydomain/publc_html/application/storage/logs
 * @return string
 */
function path_logs($str = '') {
    return BASE_DIR . '/application/storage/logs';
}

/**
 * return translated pricing from Stripe's PRICE object
 * @param string $interval e.g. day|week|month|year
 * @param int $interval_count e.g. 2 (for 2 weeks)
 * @return string e.g. Month | 2 Months
 */
function subscriptionFormatRenewalInterval($interval_count = 1, $interval = '') {

    //validate
    if ($interval == '') {
        return '---';
    }

    //translate intervals
    $lang = [
        'day' => ucwords(__('lang.day')),
        'week' => ucwords(__('lang.week')),
        'month' => ucwords(__('lang.month')),
        'year' => ucwords(__('lang.year')),
    ];

    $lang_plural = [
        'day' => ucwords(__('lang.days')),
        'week' => ucwords(__('lang.weeks')),
        'month' => ucwords(__('lang.months')),
        'year' => ucwords(__('lang.years')),
    ];

    //validate
    if (!array_key_exists($interval, $lang) || !is_numeric($interval_count)) {
        return;
    }

    if ($interval_count > 1) {
        return $interval_count . ' ' . $lang_plural[$interval];
    } else {
        return $lang[$interval];
    }

}

/**
 * return formatted money. If currency is system one, then use system settings
 * else use Akounting money formattings
 * @source https://github.com/akaunting/money
 * @param int $amount
 * @param string $currency e.g. USG
 * @return string e.g. Month | 2 Months
 */
function subscriptionFormatMoney($amount = '', $currency = '') {

    //currency is same as system
    if (strtolower($currency) == strtolower(config('system.settings_system_currency_code'))) {
        return runtimeMoneyFormat($amount);
    }

    //format using Akounting
    return money($amount, $currency);

}

/**
 * return session error message, it exists and reset the session error data
 * else use Akounting money formattings
 * @source https://github.com/akaunting/money
 * @param int $amount
 * @param string $currency e.g. USG
 * @return string e.g. Month | 2 Months
 */
function sessionErrorMessage() {

    //get message
    $message = session('error_message');

    //do we have an error message
    if ($message != '') {
        //reset message
        session(['error_message' => '']);
        return $message;
    } else {
        return __('lang.error_request_could_not_be_completed');
    }

}

/**
 * return a 'yes' or 'no' value for storing in database
 * value of coming from a checkbox (on or null)
 * @return string
 */
function runtimeDBCheckBoxYesNo($value = '') {
    if ($value == 'on') {
        return 'yes';
    } else {
        return 'no';
    }
}

/**
 * return a 'enabled' or 'disabled' value for storing in database
 * value of coming from a checkbox (on or null)
 * @return string
 */
function runtimeDBCheckBoxEnabledDisabled($value = '') {
    if ($value == 'on') {
        return 'enabled';
    } else {
        return 'disabled';
    }
}

/**
 * if a custom field is required, it will add the 'required' class to make
 * the form field label bold
 * @return string
 */
function runtimeCustomFieldsRequiredCSS($value = '') {
    if ($value == 'yes') {
        return 'required';
    }
    return;
}

/**
 * if a custom field is required, it will add the '*'
 * to show that a field is required
 * @return string
 */
function runtimeCustomFieldsRequiredAsterix($value = '') {
    if ($value == 'yes') {
        return '*';
    }
    return;
}

/**
 * return the custom field value for a given custom field
 * @paran string $file_name the name of the files, from the custom fields table
 * @paran string $file_name the name of the files, from the custom fields table
 * @return string
 */
function customFieldValue($name = '', $obj = '', $type = 'text') {

    //text
    if ($type == 'text') {
        return ($obj[$name] == '') ? '---' : $obj[$name];
    }

    //check box
    if ($type == 'checkbox') {
        return ($obj[$name] == 'on') ? __('lang.checked_custom_fields') : '---';
    }

    //date
    if ($type == 'date') {
        return runtimeDate($obj[$name]);
    }

    return $obj[$name];
}

/**
 * same as above, but used for display and returns more ---'s
 * @paran string $file_name the name of the files, from the custom fields table
 * @paran string $file_name the name of the files, from the custom fields table
 * @return string
 */
function customFieldValueDisplay($name = '', $obj = '', $type = 'text') {

    //text
    if ($obj[$name] == '') {
        $obj[$name] = '---';
    }

    //date
    if ($type == 'date') {
        return runtimeDate($obj[$name]);
    }

    //checkbox
    if ($type == 'checkbox') {
        return ($obj[$name] == 'on') ? __('lang.selected') : '---';
    }

    //numbers
    if ($type == 'number' || $type == 'decimal') {
        return ($obj[$name] == 0) ? '---' : $obj[$name];
    }

    return $obj[$name];
}

/**
 * show the correct drop down menu for (archiving or activating)
 * @param $actual_value string yes|no|active|etc [this value comes from the database]
 * @return string hidden | visible
 */
function runtimeActivateOrAchive($type = '', $value = '') {

    //archive drop down button
    if ($type == 'archive-button' && $value == "archived") {
        return 'hidden';
    }

    //archive drop down button
    if ($type == 'activate-button' && $value == "active") {
        return 'hidden';
    }

    //archived icon
    if ($type == 'archived-icon' && $value == "active") {
        return 'hidden';
    }

    //archived item notice
    if ($type == 'archived-notice' && $value == "active") {
        return 'hidden';
    }

    return;
}

/**
 * show archiving buttons and labels
 * @return bool
 */
function runtimeArchivingOptions() {

    if (auth()->user()->is_team) {
        return true;
    }

}

/**
 * Remove unwanted characters from a tag
 *
 * @return string cleaned tag
 */
function _clean($string = '') {
    return $string;
}

/**
 * determine if the settings icon should be displayed for the custom field
 * @return string
 */
function runtimeCustomFieldsSettingsVisibility($value = '') {
    if ($value == '') {
        return 'hidden';
    }
    return;
}

/**
 * set active tabs menu for custom fields
 * @return string
 */
function runtimeCustomsFieldsTabs($value1 = '', $value2 = '') {
    if ($value1 == $value2 && $value1 != '') {
        return 'active';
    }
    return;
}

/**
 * convert a json payload into select <options>
 * @param string $payload the json string from the database
 * @param bool $selected if the options should be preselected (needed in the setting side, where we want to show as tags)
 * @return string
 */
function runtimeCustomFieldsJsonLists($payload = '', $selected = false) {

    //validate
    if ($payload == '' || $payload == null) {
        return;
    }

    //take payload and change decode to an array
    $options = json_decode($payload);

    //create the select <options>
    if ($options) {
        $select = ($selected) ? 'selected' : '';
        $list = '';
        foreach ($options as $option) {
            $list .= "<option value=\"$option\" $select>$option</option>";
        }
        return $list;
    }

}

/**
 * check if the current user has permission to delete selected user
 * @param obj $user user model
 * @return string
 */
function runtimeTeamPermissionDelete($user = '') {

    //no permissions generally
    if (!auth()->user()->is_admin && auth()->user()->role->role_team != 3) {
        return false;
    }

    //cannot delete primary admin
    if ($user->primary_admin == 'yes') {
        return false;
    }

    //cannot delete yourself
    if ($user->id == auth()->user()->id) {
        return false;
    }

    //cannot delete admin user if you are not an admin user
    if ($user->is_admin && !auth()->user()->is_admin) {
        return false;
    }

    return true;
}

/**
 * check if the current user has permission to edit selected user
 * @param obj $user user model
 * @return bool
 */
function runtimeTeamPermissionEdit($user = '') {

    //no permissions generally
    if (!auth()->user()->is_admin && auth()->user()->role->role_team != 3) {
        return false;
    }

    //cannot edit primary admin
    if ($user->primary_admin == 'yes') {
        return false;
    }

    //cannot edit admin user if you are not an admin user
    if ($user->is_admin && !auth()->user()->is_admin) {
        return false;
    }

    return true;
}

/**
 * permission to add an administrator level user
 * @param int $role role id
 * @return bool
 */
function runtimeTeamCreateAdminPermissions($role = '') {

    //not an admin role
    if ($role > 1) {
        return true;
    }

    //no permissions generally
    if (auth()->user()->is_admin) {
        return true;
    }

    return false;
}

/**
 * check if any item in the list is true (i.e. visible) so as to make whole menu visible
 * @param array $list bool states of menu items in the group
 * @return bool
 */
function runtimeGroupMenuVibility($list = []) {

    foreach ($list as $value) {
        if ($value === true) {
            return true;
        }
    }
    return false;
}

/**
 * temporarily add full path for ajax repsonse with image paths. relative paths
 * are not alwyas working for images
 * @param string $test text body (usually a comment or tinymce text area)
 * @return bool
 */
function runtimeTempImagePathFix($text = '') {

    return preg_replace('%src="storage/files%', 'src="/storage/files', $text);

}

/**
 * @param bool $required
 * @return string
 */
function runtimeWebformRequiredBold($required) {

    return ($required === true) ? 'required' : '';

}

/**
 * @param bool $required
 * @return string
 */
function runtimeWebformRequiredAsterix($required) {

    return ($required === true) ? '*' : '';

}

/**
 * Visibility of various form fiels in the add-edit modal for articles
 * @param string value normally coming from the database
 * @return string css hidden|null
 */
function runtimeVisibilityKBArticle($field = '', $category_type = '') {

    //we are creating in a type of category that matches this content type (so show the editor or the embed box, respectively)
    if ($field != '') {
        if ($field == $category_type) {
            return;
        }
    }

    //hide this element
    return 'hidden';
}

/**
 * for correcting symbols that are not showing, such as euro
 * @param string $number current users setting
 * @param string $spanid if we want to wrap the figure in a span
 * @return string css setting
 */
function runtimeMoneyFormatPDF($number = '', $span_id = "") {

    $number = runtimeNumberFormat($number);

    //are we wrapping in a span
    if ($span_id != '') {
        $number = '<span id="' . $span_id . '">' . $number . '</span>';
    }

    return runtimePDFCharacters(config('system.currency_symbol_left')) . $number . runtimePDFCharacters(config('system.currency_symbol_right'));
}

/** -------------------------------------------------------------------------
 * A fix for some currency symbolsnow showing correctly in the PDF
 * To add new currencies, add a pair of the currency and its corresponding
 * html code.
 *
 * You then use this function in the PDF templatem, in every place that a
 * currency symbol is displayed
 *
 * @param string $value the real currency symply (e.g. $)
 * @return string corresponding html code for the currency symbol
 * -------------------------------------------------------------------------*/
function runtimePDFCharacters($value = '') {

    //currency symbols (you can add more here)
    $search = array('€', '₫');

    //corresponding HTML codes for the currency symbols (in the correct order as above)
    $replace = array('&euro;', '&#8363;');

    //return the html code for the currency symbol
    return str_replace($search, $replace, $value);

}

/** -------------------------------------------------------------------------
 * Delete all flash messages (success & error). This is needed when sometimes
 * we are using ajax and keep getting a previous flash message.
 *
 * Just add this to any method in the controllers
 *
 * @return null
 * -------------------------------------------------------------------------*/
function runtimeClearFlashMessages() {

    request()->session()->forget('flash-error-message');
    request()->session()->forget('error-notification-long');
    request()->session()->forget('success-notification');
    request()->session()->forget('success-notification-long');

}

/**
 * get a cover image for projects for task covers
 * @return string
 */
function getCoverImage($directory = '', $filename = '') {

    //check if cover exists
    if (Storage::exists("/files/$directory/$filename") && $filename != '' && $directory != '') {
        return "style=\"background-image: url(storage/files/$directory/$filename)\"";
    }

    //default placeholder cover
    return "style=\"background-image: url('public/images/placeholder.jpg')\"";
}

/**
 * return a singular or translated string
 * @param string $interval e.g. day|week|month|year
 * @param int $interval_count e.g. 2 (for 2 weeks)
 * @return string e.g. Month | 2 Months
 */
function runtimeIntervalPlural($interval_count = 1, $interval = '') {

    //validate
    if ($interval == '') {
        return '---';
    }

    //translate intervals
    $lang = [
        'day' => ucwords(__('lang.day')),
        'week' => ucwords(__('lang.week')),
        'month' => ucwords(__('lang.month')),
        'year' => ucwords(__('lang.year')),
    ];

    $lang_plural = [
        'day' => ucwords(__('lang.days')),
        'week' => ucwords(__('lang.weeks')),
        'month' => ucwords(__('lang.months')),
        'year' => ucwords(__('lang.years')),
    ];

    //validate
    if (!array_key_exists($interval, $lang) || !is_numeric($interval_count)) {
        return;
    }

    if ($interval_count == 1) {
        return $interval_count . ' ' . $lang[$interval];
    } else {
        return $interval_count . ' ' . $lang_plural[$interval];
    }

}

/**
 * show or hide task recurring icom
 * @param string $var current users setting
 * @return string css setting
 */
function runtimeTaskRecurringIcon($var = '') {
    if ($var != 'yes') {
        return 'hidden';
    }
    return;
}

/**
 * return formatted proposal id (e.g. PROP000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeProposalIdFormat($proposal_id = '') {
    //add the zero's
    $prefix = config('system.settings_proposals_prefix');
    //return
    if (is_numeric($proposal_id)) {
        return $prefix . str_pad($proposal_id, 5, '0', STR_PAD_LEFT);
    } else {
        return '---';
    }
}

/**
 * return formatted contract id (e.g. CONTR000024)
 * @param numeric bill_invoiceid
 * @return string checked | null
 */
function runtimeContractIdFormat($contract_id = '') {
    //add the zero's
    $prefix = config('system.settings_contracts_prefix');
    //return
    if (is_numeric($contract_id)) {
        return $prefix . str_pad($contract_id, 5, '0', STR_PAD_LEFT);
    } else {
        return '---';
    }
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeProposalStatusColors($value = '', $type = 'label') {

    //get the settings value
    switch ($value) {
    case 'draft':
        $colour = 'default';
        break;
    case 'new':
        $colour = 'info';
        break;
    case 'accepted':
        $colour = 'success';
        break;
    case 'declined':
        $colour = 'danger';
        break;
    case 'revised':
        $colour = 'primary';
        break;
    case 'expired':
        $colour = 'warning';
        break;
    default:
        $colour = 'default';
        break;
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * bootstrap class, based on value
 * @param string value the status of the task
 * @param string type lable|background
 * @return string bootstrap label class
 */
function runtimeContractStatusColors($value = '', $type = 'label') {

    //get the settings value
    switch ($value) {
    case 'draft':
        $colour = 'default';
        break;
    case 'new':
        $colour = 'info';
        break;
    case 'accepted':
        $colour = 'success';
        break;
    case 'declined':
        $colour = 'danger';
        break;
    case 'revised':
        $colour = 'primary';
        break;
    case 'expired':
        $colour = 'danger';
        break;
    default:
        $colour = 'default';
        break;
    }

    //return the css
    return bootstrapColors($colour, $type);
}

/**
 * return lead name with title
 * @return string
 */
function runtimeLeadNameTitle($lead_firstname = '', $lead_lastname = '', $lead_title = '') {

    return $lead_firstname . ' ' . $lead_lastname;

}

/**
 * get the cover image for projects for task covers
 * @return string
 */
function getDocumentHeroImage($directory = '', $filename = '', $updated = 'no') {

    //use image updated by the user
    if ($updated == 'yes') {
        if (Storage::exists("/files/$directory/$filename") && $filename != '' && $directory != '') {
            return "style=\"background-image: url(storage/files/$directory/$filename)\"";
        }
    }

    //default placeholder cover
    return "style=\"background-image: url('public/documents/images/default-background.jpg')\"";
}

/**
 * set the color of text from a dbase value
 * @return string
 */
function getFontColor($color = '') {

    //no color
    if ($color == '') {
        return;
    }

    //default placeholder cover
    return "style=\"color: $color\"";
}

/**
 * check if the document is is editing mode
 * @return string
 */
function documentEditingModeCheck1($mode = '') {

    //no color
    if ($mode == 'editing') {
        return 'js-doc-editing';
    }
    return;
}

/**
 * check if the document is is editing mode
 * @return string
 */
function documentEditingModeCheck2($mode = '') {

    //no color
    if ($mode != 'editing') {
        return 'hidden';
    }
    return;
}

/**
 * show the correct ribbon,based on status
 * @param string $value the actual value of the button (i.e. its css status)
 * @param string $status the database status of the document
 */
function documentRibbonVisibility($value = 'foo', $status = 'bar') {

    //hide the button
    if ($value != $status) {
        return 'hidden';
    }
    return;
}