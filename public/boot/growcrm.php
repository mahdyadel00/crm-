<?php

/*----------------------------------------------------------------
 * NEXTLOOP
 * @description
 * Check system meets some minimum requirements before continuing
 *   - PHP Version ( >= 7.2.5)
 *   - Writable directory permissions
 *
 * @updated
 * 26 October 2020
 *---------------------------------------------------------------*/
if (!defined('SANITYCHECKS')) {
    die('Access is not permitted');
}

$errors = 0;
$messages_chmod = '';
$messages_php = '';

$paths = [
    '/updates',
    '/storage',
    '/storage/avatars',
    '/storage/logos',
    '/storage/logos/clients',
    '/storage/logos/app',
    '/storage/files',
    '/storage/temp',
    '/application/storage/app',
    '/application/storage/app/public',
    '/application/storage/cache',
    '/application/storage/cache/data',
    '/application/storage/debugbar',
    '/application/storage/framework',
    '/application/storage/framework/cache',
    '/application/storage/framework/cache/data',
    '/application/storage/framework/sessions',
    '/application/storage/framework/testing',
    '/application/storage/framework/views',
    '/application/storage/logs',
    '/application/bootstrap/cache',
    '/application/storage/app/purifier',
    '/application/storage/app/purifier/HTML',
];

//Subdomain - Subfolder Fix
if (!is_dir(BASE_PATH . '/application')) {
    die('Error! - You cannot access the CRM from this url');
}

//check directoies
foreach ($paths as $key => $value) {
    if (!is_writable(BASE_PATH . $value)) {
        $messages_chmod .= '<tr><td class="p-l-15">' . BASE_PATH . $value . '</td><td class="x-td-checks" width="40px"><span class="x-checks x-check-failed text-danger font-18"><i class="sl-icon-close"></i></span></td></tr>';
        $errors++;
    } else {
        $messages_chmod .= '<tr><td class="p-l-15">' . BASE_PATH . $value . '</td><td class="x-td-checks" width="40px"><span class="x-checks x-check-passed text-info font-18"><i class="sl-icon-check"></i></span></td></tr>';
    }
}

//check minimum php version
if (version_compare(PHP_VERSION, '7.2.5', ">=")) {
    $messages_php = '<tr><td class="p-l-15">PHP >= v7.2.5 </td><td class="x-td-checks" width="40px"><span class="x-checks x-check-passed text-info font-18"><i class="sl-icon-check"></i></span></td></tr>';
} else {
    $messages_php = '<tr><td class="p-l-15">PHP >= v7.2.5 </td><td class="x-td-checks" width="40px"><span class="x-checks x-check-failed text-danger font-18"><i class="sl-icon-close"></i></span></td></tr>';
    $errors++;

}

//page
$page = '
<!DOCTYPE html><html lang="en" class="team"><head><link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet"><link href="public/themes/default/css/style.css" rel="stylesheet"><link rel="stylesheet" href="public/vendor/css/vendor.css"></head>
<body class="setup-prechecks"><div class="x-wrapper"><div class="col-12 p-t-40 card-no-border"><div class="card"><div class="card-body"><div class="text-center"><h3 class="card-title">GROW CRM</h3>
<h5>System Check</h5><div><img src="public/images/system-checks.png" width="300" alt="system checks failed" /></div><p class="card-text">The following (minimum system requirements) must be met before you can continue. See <a href="https://growcrm.io/documentation/2-installation/" target="_blank">documentation</a> for details.</p>
</div><div class="m-t-20"></br></br><h5 class="text-info"> PHP Requirement</h5><table class="table table-bordered w-100">' . $messages_php . '</table>
</br></br><h5 class="text-info"> Folder - Writable Permission</h5><table class="table table-bordered w-100">' . $messages_chmod . '</table></div><div class="text-center"><a href="/" class="btn btn-info">Retry</a></div></div></div></div></div></body><html>';

//do we have directory errors
if ($errors > 0) {
    die($page);
}
