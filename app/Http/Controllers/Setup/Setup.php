<?php

/** --------------------------------------------------------------------------------
 * This controller manages the business logic for the setup wizard
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Setup\DatabaseResponse;
use App\Http\Controllers\Setup\FinishResponse;
use App\Http\Controllers\Setup\IndexResponse;
use App\Http\Controllers\Setup\RequirementsResponse;
use App\Http\Controllers\Setup\SettingsResponse;
use App\Http\Controllers\Setup\UserResponse;
use App\Repositories\EnvRepository;
use Illuminate\Support\Facades\DB;
use Validator;

class Setup extends Controller {

    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('guest');

    }

    /**
     * Display Setup first page
     * @return blade view | ajax view
     */
    public function index() {

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Display Setup first page
     * @return blade view | ajax view
     */
    public function serverInfo() {

        //show the view
        return view('pages/setup/serverinfo');
    }

    /**
     * [source] https://laravel.com/docs/7.x/installation#server-requirements
     * PHP >= 7.2.5
     * BCMath PHP Extension
     * Ctype PHP Extension
     * Fileinfo PHP extension
     * JSON PHP Extension
     * Mbstring PHP Extension
     * OpenSSL PHP Extension
     * PDO PHP Extension
     * Tokenizer PHP Extension
     * XML PHP Extension
     * @return blade view | ajax view
     */
    public function checkRequirements() {

        $error['count'] = 0;

        //server requirements checks
        $requirements['php_version'] = version_compare(PHP_VERSION, '7.3.0', ">=");
        $requirements['bcmath'] = extension_loaded("bcmath");
        $requirements['mysql'] = extension_loaded("mysqli");
        $requirements['ctype'] = extension_loaded("ctype");
        $requirements['fileinfo'] = extension_loaded("fileinfo");
        $requirements['json'] = extension_loaded("json");
        $requirements['mbstring'] = extension_loaded("mbstring");
        $requirements['openssl'] = extension_loaded("openssl");
        $requirements['pdo'] = defined('PDO::ATTR_DRIVER_NAME');
        $requirements['tokenizer'] = extension_loaded("tokenizer");
        $requirements['xml'] = extension_loaded("xml");
        $requirements['gd'] = extension_loaded("gd");
        $requirements['fileinfo'] = extension_loaded("fileinfo");

        //directory (writable checks)
        $requirements['dir_updates'] = is_writable(BASE_DIR . '/updates');
        $requirements['dir_storage'] = is_writable(BASE_DIR . '/storage');
        $requirements['dir_storage_avatars'] = is_writable(BASE_DIR . '/storage/avatars');
        $requirements['dir_storage_logos'] = is_writable(BASE_DIR . '/storage/logos');
        $requirements['dir_storage_logos_clients'] = is_writable(BASE_DIR . '/storage/logos/clients');
        $requirements['dir_storage_logos_app'] = is_writable(BASE_DIR . '/storage/logos/app');
        $requirements['dir_storage_files'] = is_writable(BASE_DIR . '/storage/files');
        $requirements['dir_storage_temp'] = is_writable(BASE_DIR . '/storage/temp');
        $requirements['dir_app_storage_app'] = is_writable(BASE_DIR . '/application/storage/app');
        $requirements['dir_app_storage_app_public'] = is_writable(BASE_DIR . '/application/storage/app/public');
        $requirements['dir_app_storage_cache'] = is_writable(BASE_DIR . '/application/storage/cache');
        $requirements['dir_app_storage_cache_data'] = is_writable(BASE_DIR . '/application/storage/cache/data');
        $requirements['dir_app_storage_debugbar'] = is_writable(BASE_DIR . '/application/storage/debugbar');
        $requirements['dir_app_storage_framework'] = is_writable(BASE_DIR . '/application/storage/framework');
        $requirements['dir_app_storage_framework_cache'] = is_writable(BASE_DIR . '/application/storage/framework/cache');
        $requirements['dir_app_storage_framework_cache_data'] = is_writable(BASE_DIR . '/application/storage/framework/cache/data');
        $requirements['dir_app_storage_framework_sessions'] = is_writable(BASE_DIR . '/application/storage/framework/sessions');
        $requirements['dir_app_storage_framework_testing'] = is_writable(BASE_DIR . '/application/storage/framework/testing');
        $requirements['dir_app_storage_framework_views'] = is_writable(BASE_DIR . '/application/storage/framework/views');
        $requirements['dir_app_storage_logs'] = is_writable(BASE_DIR . '/application/storage/logs');
        $requirements['dir_app_bootstrap_cache'] = is_writable(BASE_DIR . '/application/bootstrap/cache');
        $requirements['dir_app_storage_app_purifier'] = is_writable(BASE_DIR . '/application/storage/app/purifier');
        $requirements['dir_app_storage_app_purifier_html'] = is_writable(BASE_DIR . '/application/storage/app/purifier/HTML');

        //files (writable checks)
        $requirements['dir_app_env'] = is_writable(BASE_DIR . '/application/.env');

        //check if we had errors
        foreach ($requirements as $key => $value) {
            if (!$value) {
                $error['count']++;
            }
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'requirements' => $requirements,
            'error' => $error,
        ];

        //show the view
        return new RequirementsResponse($payload);
    }

    /**
     * Display database form page
     * @return blade view | ajax view
     */
    public function showDatabase() {

        $error['count'] = 0;

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new DatabaseResponse($payload);
    }

    /**
     * Update database and show settings form
     * @return blade view | ajax view
     */
    public function updateDatabase(EnvRepository $envrepo) {

        $error['count'] = 0;

        //validate
        $validator = Validator::make(request()->all(), [
            'database_host' => 'required',
            'database_port' => 'required',
            'database_name' => 'required',
            'database_username' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('Fill in al required fields'));
        }

        //check if password has disallowed characters "
        if (request()->filled('database_password')) {
            if (stripos(request('database_password'), '"')) {
                abort(409, 'You MySQL password contains a disallowed character "');
            }
        }

        //try and connect to database
        $dbhost = request('database_host');
        $dbport = request('database_port');
        $dbname = request('database_name');
        $dbuser = request('database_username');
        $dbpass = request('database_password');

        //connect
        $connection = @mysqli_connect($dbhost, $dbuser, $dbpass) or abort(409, __('Error(1001): Unable to connect to your database. Check details and try again'));

        //select the database
        @mysqli_select_db($connection, $dbname) or abort(409, __('Error(1002): Unable to connect to your database. Check details and try again'));

        //update the .env file
        $data = [
            'DB_HOST' => $dbhost,
            'DB_PORT' => $dbport,
            'DB_DATABASE' => $dbname,
            'DB_USERNAME' => $dbuser,
            'DB_PASSWORD' => $dbpass,
        ];
        if (!$envrepo->updateDatabase($data)) {
            abort(409, __('Error(1003): Unable to save the (.env) file'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'error' => $error,
        ];

        //show the view
        return new SettingsResponse($payload);
    }

    /**
     * Update settings and display user form
     * @return blade view | ajax view
     */
    public function updateSettings() {

        //validate
        $validator = Validator::make(request()->all(), [
            'settings_company_name' => 'required',
            'settings_system_timezone' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('Fill in al required fields'));
        }

        //import sql file
        $sql_file = BASE_DIR . '/setup.sql';

        //validate file
        if (!is_file($sql_file)) {
            abort(409, __('Error(1004): A required file (/setup.sql) is missing'));
        }

        //import the file
        DB::unprepared(file_get_contents($sql_file));

        //update default settings
        if (!$settings = \App\Models\Settings::Where('settings_id', 1)->first()) {
            abort(409, __('Error(1007): Unable to load database settings'));
        }

        //update default categories dates
        \App\Models\Category::where('category_system_default', 'yes')
            ->update(['category_created' => now()]);

        //update default categories dates
        \App\Models\KbCategories::where('kbcategory_system_default', 'yes')
            ->update(['kbcategory_created' => now()]);

        //update settings
        $settings->settings_company_name = request('settings_company_name');
        $settings->settings_installation_date = now();
        $settings->save();

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
        ];

        //show the view
        return new UserResponse($payload);
    }

    /**
     * Update admin user and display finish page
     * @return blade view | ajax view
     */
    public function updateUser(EnvRepository $envrepo) {

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            abort(409, __('Fill in al required fields'));
        }

        //update default user
        if (!$user = \App\Models\User::Where('id', 1)->first()) {
            abort(409, __('Error(1005): Admin user could not be updated'));
        }
        $user->first_name = request('first_name');
        $user->last_name = request('last_name');
        $user->email = request('email');
        $user->password = bcrypt(request('password'));
        $user->created = now();
        $user->updated = now();
        $user->last_seen = now();
        $user->creatorid = 0;

        $user->save();

        //delete setup sql file
        @unlink(BASE_DIR . '/setup.sql');

        //final .env file update
        if (!$envrepo->completeSetup()) {
            abort(409, __('Error(1006): - Setup could not complete'));
        }

        //reponse payload
        $payload = [
            'page' => $this->pageSettings(),
            'cronjob_path' => '/usr/local/bin/php ' . BASE_DIR . '/application/artisan schedule:run >> /dev/null 2>&1',
        ];

        //show the view
        return new FinishResponse($payload);
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'page' => 'setup',
            'meta_title' => 'Application Setup',
        ];
        return $page;
    }

}