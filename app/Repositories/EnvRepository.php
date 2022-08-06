<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all writing to the .env file
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Log;

class EnvRepository {

    /**
     * The full file path to the .env file
     */
    protected $env_file_path;

    /**
     * Inject dependecies
     */
    public function __construct() {

        $this->env_file_path = BASE_DIR . '/application/.env';

        //validate
        if (!is_writable($this->env_file_path) || !is_file($this->env_file_path)) {
            Log::critical("the file is not writable (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

    }

    /**
     * Replace the database information in the .env file
     * save the file
     * @param array $data the payload
     * @return bool
     */
    public function updateDatabase($data = []) {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change DB_HOST
        if (isset($data['DB_HOST'])) {
            $new = "DB_HOST=" . $data['DB_HOST'];
            $env = preg_replace('/DB_HOST=.*$/m', $new, $env);
        }

        //change DB_PORT
        if (isset($data['DB_PORT'])) {
            $new = "DB_PORT=" . $data['DB_PORT'];
            $env = preg_replace('/DB_PORT=.*$/m', $new, $env);
        }

        //change DB_DATABASE
        if (isset($data['DB_DATABASE'])) {
            $new = "DB_DATABASE=" . $data['DB_DATABASE'];
            $env = preg_replace('/DB_DATABASE=.*$/m', $new, $env);
        }

        //change DB_USERNAME
        if (isset($data['DB_USERNAME'])) {
            $new = "DB_USERNAME=" . $data['DB_USERNAME'];
            $env = preg_replace('/DB_USERNAME=.*$/m', $new, $env);
        }

        //change DB_PASSWORD
        if (isset($data['DB_PASSWORD']) && $data['DB_PASSWORD'] != '') {
            $new = 'DB_PASSWORD="' . $data['DB_PASSWORD'] . '"';
            $env = preg_replace('/DB_PASSWORD=.*$/m', $new, $env);
        }

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        //failed
        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;

    }

    /**
     * Setup wizard process
     * save the .env file with updated information
     * @return bool
     */
    public function completeSetup() {

        // Read .env-file
        $env = file_get_contents($this->env_file_path);

        //change APP_URL
        $new = "APP_URL=" . url('/');
        $env = preg_replace('/APP_URL=.*$/m', $new, $env);

        //change SETUP_STATUS
        $new = "SETUP_STATUS=COMPLETED\r\n";
        $env = preg_replace('/SETUP_STATUS=.*$/m', $new, $env);

        //change SESSION_DRIVER
        $new = "SESSION_DRIVER=database\r\n";
        $env = preg_replace('/SESSION_DRIVER=.*$/m', $new, $env);

        //change SESSION_DRIVER
        $new = "QUEUE_DRIVER=database\r\n";
        $env = preg_replace('/QUEUE_DRIVER=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEBUG=true\r\n";
        $env = preg_replace('/APP_DEBUG=.*$/m', $new, $env);

        //set app logging level
        $new = "APP_LOG_LEVEL=error\r\n";
        $env = preg_replace('/APP_LOG_LEVEL=.*$/m', $new, $env);

        //disable the debug toolbar
        $new = "APP_DEBUG_TOOLBAR=false\r\n";
        $env = preg_replace('/APP_DEBUG_TOOLBAR=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEBUG_JAVASCRIPT=false\r\n";
        $env = preg_replace('/APP_DEBUG_JAVASCRIPT=.*$/m', $new, $env);

        //enable logging
        $new = "APP_DEMO_MODE=false\r\n";
        $env = preg_replace('/APP_DEMO_MODE=.*$/m', $new, $env);

        //change APP_ENV
        $new = "APP_ENV=production\r\n";
        $env = preg_replace('/APP_ENV=.*$/m', $new, $env);

        // overwrite the .env with the new data
        if (file_put_contents($this->env_file_path, $env)) {
            return true;
        }

        Log::critical("unable to write to the file (.env)", ['process' => '[EnvRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        return false;
    }

}