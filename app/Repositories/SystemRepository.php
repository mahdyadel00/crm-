<?php

/** --------------------------------------------------------------------------------
 * Various common system routines
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

class SystemRepository {

    /**
     * Inject dependecies
     */
    public function __construct() {

    }

    /**
     * check server requirements for the Lravel Execel module
     * @source https://docs.laravel-excel.com/3.1/getting-started/installation.html
     * @return array
     */
    public function serverRequirementsExcel() {

        //vars
        $status = 'passed';
        $requirements = [];

        //each requirement
        $check = [
            'iconv' => extension_loaded("iconv"),
            'simplexml' => extension_loaded("simplexml"),
            'xmlreader' => extension_loaded("xmlreader"),
            'zlib' => extension_loaded("zlib"),
        ];

        //check each requirement
        foreach ($check as $key => $value) {
            if ($value) {
                $requirements[$key] = true;
            } else {
                $requirements[$key] = false;
                $status = 'failed';
            }
        }

        return [
            'status' => $status,
            'requirements' => $requirements,
        ];
    }

}