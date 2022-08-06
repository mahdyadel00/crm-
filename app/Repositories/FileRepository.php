<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File as Files;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Image;
use Log;

class FileRepository {

    /**
     * The files repository instance.
     */
    protected $files;

    /**
     * Inject dependecies
     */
    public function __construct(File $files) {
        $this->files = $files;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @param array $data optional data payload
     * @return object file collection
     */
    public function search($id = '', $data = []) {

        $files = $this->files->newQuery();

        //default - always apply filters
        if (!isset($data['apply_filters'])) {
            $data['apply_filters'] = true;
        }

        // all client fields
        $files->selectRaw('*');

        //joins
        $files->leftJoin('users', 'users.id', '=', 'files.file_creatorid');
        $files->leftJoin('clients', 'clients.client_id', '=', 'files.file_clientid');

        //default where
        $files->whereRaw("1 = 1");

        //[filter] by file id
        if (is_numeric($id)) {
            $files->where('file_id', $id);
        }

        //[data filter] resource_id
        if (isset($data['fileresource_id'])) {
            $files->where('fileresource_id', $data['fileresource_id']);
        }

        //[data filter] resource_type
        if (isset($data['fileresource_type'])) {
            $files->where('fileresource_type', $data['fileresource_type']);
        }

        //apply filters
        if ($data['apply_filters']) {

            //filters: id
            if (request()->filled('filter_file_id')) {
                $files->where('file_id', request('filter_file_id'));
            }

            //filters: fileresource_type
            if (request()->filled('fileresource_type')) {
                $files->where('fileresource_type', request('fileresource_type'));
            }

            //filters: fileresource_type
            if (request()->filled('fileresource_id')) {
                $files->where('fileresource_id', request('fileresource_id'));
            }

            //filters: client
            if (request()->filled('filter_file_clientid')) {
                $files->where('file_clientid', request('filter_file_clientid'));
            }

            //filters: visibility
            if (request()->filled('filter_file_visibility_client')) {
                $files->where('file_visibility_client', request('filter_file_visibility_client'));
            }

            //filters: files unique key (used to identify files that were uploaded in one go)
            if (request()->filled('filter_file_upload_unique_key')) {
                $files->where('file_upload_unique_key', request('filter_file_upload_unique_key'));
            }

            //resource filtering
            if (request()->filled('fileresource_type')) {
                $files->where('fileresource_type', request('fileresource_type'));
            }

            if (request()->filled('fileresource_id')) {
                $files->where('fileresource_id', request('fileresource_id'));
            }
        }

        //search: various client columns and relationships (where first, then wherehas)
        if (request()->filled('search_query') || request()->filled('query')) {
            $files->where(function ($query) {
                $query->Where('file_id', '=', request('search_query'));
                $query->orWhere('file_created', 'LIKE', '%' . date('Y-m-d', strtotime(request('search_query'))) . '%');
                $query->orWhere('file_filename', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('file_extension', '=', request('search_query'));
                $query->orWhere('file_size', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('client_company_name', 'LIKE', '%' . request('search_query') . '%');
                $query->orWhere('client_company_name', 'LIKE', '%' . request('search_query') . '%');
            });
        }

        //sorting
        if (in_array(request('sortorder'), array('desc', 'asc')) && request('orderby') != '') {
            //direct column name
            if (Schema::hasColumn('files', request('orderby'))) {
                $files->orderBy(request('orderby'), request('sortorder'));
            }
            //others
            switch (request('orderby')) {
            case 'added_by':
                $files->orderBy('first_name', request('sortorder'));
                break;
            case 'visibility':
                $files->orderBy('file_visibility_client', request('sortorder'));
                break;
            }
        } else {
            //default sorting
            $files->orderBy('file_id', 'desc');
        }

        // Get the results and return them.
        return $files->paginate(config('system.settings_system_pagination_limits'));
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return mixed object|bool
     */
    public function create($data = '') {

        //save new user
        $file = new $this->files;

        //validate
        if (!is_array($data)) {
            return false;
        }

        //data
        $file->file_creatorid = auth()->id();
        $file->file_clientid = $data['file_clientid'];
        $file->file_uniqueid = $data['file_uniqueid'];
        $file->file_upload_unique_key = $data['file_upload_unique_key'];
        $file->file_directory = $data['file_directory'];
        $file->file_filename = $data['file_filename'];
        $file->file_extension = $data['file_extension'];
        $file->file_type = $data['file_type'];
        $file->file_size = $data['file_size'];
        $file->file_thumbname = $data['file_thumbname'];
        $file->fileresource_type = $data['fileresource_type'];
        $file->fileresource_id = $data['fileresource_id'];
        $file->file_visibility_client = (request('file_visibility_client') == 'on') ? 'yes' : 'no';

        //save and return id
        if ($file->save()) {
            return $file->file_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[FileRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Process a file uploads as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. If the file is an image, create a thumbnail
     *   3. move the whole directory in the the 'files' directory
     *   4. create a database record for the attachment
     * @param array $data information payload
     * @return int
     */
    public function process($data = []) {

        //debug ref
        $debug_ref = Str::random(6);

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["file_directory"] . "/" . $data["file_filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["file_directory"];
        $new_dir_path = BASE_DIR . "/storage/files/" . $data["file_directory"];
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        $thumbname = generateThumbnailName($data["file_filename"]); //if its an image

        //file extension and file size (human readable)
        $data += [
            'file_extension' => $extension,
            'file_size' => humanFileSize(filesize($file_path)),
        ];

        //validation: file exists
        if (!file_exists($file_path)) {
            Log::error("the file could not be found", ['process' => '[FileRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path ?? '']);
            return false;
        }

        //check if the file is an image
        if (is_array(@getimagesize($file_path))) {
            //data
            $data += [
                'file_type' => 'image',
                'file_thumbname' => $thumbname,
            ];
        } else {
            //data
            $data += [
                'file_type' => 'file',
                'file_thumbname' => '',
            ];
        }

        //move directory
        Files::moveDirectory($old_dir_path, $new_dir_path, true);

        //save to database
        $file_id = $this->create($data);

        return $file_id;
    }

    /**
     * update a record
     * @param int $id record id
     * @return mixed int|bool
     */
    public function update($id) {

        //get the record
        if (!$file = $this->files->find($id)) {
            return false;
        }

        //general
        $file->file_visibility_client = (request('visible_to_client') == 'on') ? 'yes' : 'no';

        //save
        if ($file->save()) {
            return $file->file_id;
        } else {
            Log::error("unable to update record - database error", ['process' => '[FileRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

    /**
     * Process a file attachement as follow
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. move the whole directory in the the 'files' directory
     * @param array $data information payload
     * @return bool status outcome
     */
    public function processUpload($data = []) {

        //sanity
        if ($data["directory"] == '' || $data["filename"] == '') {
            Log::error("required information is missing (directory or filename)", ['process' => '[AttachmentRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["directory"] . "/" . $data["filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["directory"];
        $new_dir_path = BASE_DIR . "/storage/files/" . $data["directory"];

        //validation: file exists
        if (!file_exists($file_path)) {
            Log::error("the avatar file could not be found", ['process' => '[AttachmentRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        //check if the file is an image
        if (!is_array(getimagesize($file_path))) {
            Log::error("the avatar file is not an image", ['process' => '[AttachmentRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        //move directory
        Files::moveDirectory($old_dir_path, $new_dir_path, true);

        return true;
    }

}