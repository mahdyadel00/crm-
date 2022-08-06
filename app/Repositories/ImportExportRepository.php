<?php

/** --------------------------------------------------------------------------------
 * Handles importing csv, xls, xlxs files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;
use Illuminate\Http\Request;
use Log;

class ImportExportRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * Initial validation. Check file exists etc
     * @return bool
     */
    public function validateImport() {

        //validation of the form
        if (!request()->filled('importing-file-name') || !request()->filled('importing-file-uniqueid')) {
            Log::error("import file data is missing", ['process' => '[import-leads-repository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'project_id' => 1]);
            return false;
        }

        //validation: file exists
        $file_path = BASE_DIR . "/storage/temp/" . request('importing-file-uniqueid') . "/" . request('importing-file-name');
        if (!file_exists($file_path)) {
            Log::error("the imported file could not be found", ['process' => '[import-leads-repository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        return true;

    }

    /** -------------------------------------------------------------------------
     * Log importing errors (if any)
     * -------------------------------------------------------------------------*/
    public function logImportError($failures = [], $error_ref = '') {

        //create error log
        $message = '';

        foreach ($failures as $failure) {

            $errors = $failure->errors();

            // full table message
            $message .= '<tr>
                            <td>' . $failure->row() . '</td>
                            <td>' . $failure->attribute() . '</td>
                            <td>' . $errors[0] . '</td>
                         </tr>';
        }

        //save log to db
        if (count($failures) > 0) {
            $log = new \App\Models\Log();
            $log->log_text = 'error_log';
            $log->log_text_type = 'lang';
            $log->log_uniqueid = $error_ref;
            $log->log_payload = $message;
            $log->logresource_type = 'import';
            $log->logresource_id = 0;
            $log->save();
        }

    }

}