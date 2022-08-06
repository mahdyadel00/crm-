<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for attachements
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Attachment;
use Illuminate\Support\Facades\File;
use Image;
use Log;

class AttachmentRepository {

    /**
     * The attachments repository instance.
     */
    protected $attachments;

    /**
     * Inject dependecies
     */
    public function __construct(Attachment $attachments) {
        $this->attachments = $attachments;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object attachments collection
     */
    public function search($id = '') {

        //new query
        $attachments = $this->attachments->newQuery();

        // all client fields
        $attachments->selectRaw('*');

        //joins
        $attachments->leftJoin('users', 'users.id', '=', 'attachments.attachment_creatorid');

        //default where
        $attachments->whereRaw("1 = 1");

        //filter by id
        if (is_numeric($id)) {
            $attachments->where('attachment_id', $id);
        }

        //filters: resource type
        if (request()->filled('attachmentresource_type')) {
            $attachments->where('attachmentresource_type', request('attachmentresource_type'));
        }

        //filters: resource type
        if (request()->filled('attachmentresource_id')) {
            $attachments->where('attachmentresource_id', request('attachmentresource_id'));
        }

        //filter clients
        if (request()->filled('filter_attachment_clientid')) {
            $invoices->where('attachment_clientid', request('filter_attachment_clientid'));
        }

        //default sorting
        $attachments->orderBy('attachment_id', 'desc');

        return $attachments->paginate(1000);
    }

    /**
     * Create a new record
     * @param array $data payload data
     * @return bool|int id of new record
     */
    public function create($data = '') {

        //save new user
        $attachment = new $this->attachments;

        //validate
        if (!is_array($data)) {
            Log::error("validation error - invalid params", ['process' => '[AttachmentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }

        //data
        $attachment->attachment_creatorid = (isset($data['attachment_creatorid']))  ? $data['attachment_creatorid'] : auth()->id();
        $attachment->attachment_clientid = $data['attachment_clientid'];
        $attachment->attachment_uniqiueid = $data['attachment_uniqiueid'];
        $attachment->attachment_directory = $data['attachment_directory'];
        $attachment->attachment_filename = $data['attachment_filename'];
        $attachment->attachment_extension = $data['attachment_extension'];
        $attachment->attachment_type = $data['attachment_type'];
        $attachment->attachment_size = $data['attachment_size'];
        $attachment->attachment_thumbname = $data['attachment_thumbname'];
        $attachment->attachmentresource_type = $data['attachmentresource_type'];
        $attachment->attachmentresource_id = $data['attachmentresource_id'];

        //save and return id
        if ($attachment->save()) {
            return $attachment->attachment_id;
        } else {
            return false;
        }
    }

    /**
     * Process a file attachement as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. If the file is an image, create a thumbnail
     *   3. move the whole directory in the the 'files' directory
     * @param array $data payload data
     * @return int attachment id
     */
    public function process($data = []) {

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["attachment_directory"] . "/" . $data["attachment_filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["attachment_directory"];
        $new_dir_path = BASE_DIR . "/storage/files/" . $data["attachment_directory"];
        $extension = pathinfo($file_path, PATHINFO_EXTENSION);
        $thumbname = generateThumbnailName($data["attachment_filename"]); //if its an image

        //file extension and file size (human readable)
        $data += [
            'attachment_extension' => $extension,
            'attachment_size' => humanFileSize(filesize($file_path)),
        ];

        //validation: file exists
        if (!file_exists($file_path)) {
            Log::error("the attachment could not be found", ['process' => '[AttachmentRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path]);
            return false;
        }

        //check if the file is an image
        if (is_array(getimagesize($file_path))) {
            //data
            $data += [
                'attachment_type' => 'image',
                'attachment_thumbname' => $thumbname,
            ];
        } else {
            //data
            $data += [
                'attachment_type' => 'file',
                'attachment_thumbname' => '',
            ];
        }

        //move directory
        File::moveDirectory($old_dir_path, $new_dir_path, true);

        //save to database
        $attachment_id = $this->create($data);
    }

    /**
     * Process a file attachement as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. check that the file is an image
     *   3. move the whole directory in the the 'files' directory
     *   4. update the users record
     * @param array $data information payload
     * @return bool status outcome
     */
    public function processAvatar($data = []) {

        //sanity
        if ($data["directory"] == '' || $data["filename"] == '') {
            Log::error("required information is missing (directory or filename)", ['process' => '[AttachmentRepository]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data]);
            return false;
        }

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["directory"] . "/" . $data["filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["directory"];
        $new_dir_path = BASE_DIR . "/storage/avatars/" . $data["directory"];

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
        File::moveDirectory($old_dir_path, $new_dir_path, true);

        return true;
    }

    /**
     * Process a file attachement as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. check that the file is an image
     *   3. move the whole directory in the the 'files' directory
     *   4. update the users record
     * @param array $data information payload
     * @return bool status outcome
     */
    public function processClientLogo($data = []) {

        //sanity
        if ($data["directory"] == '' || $data["filename"] == '') {
            Log::error("required information is missing (directory or filename)", ['process' => '[attachments-client-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data] ?? '');
            return false;
        }

        //path to this directory in the temp folder
        $file_path = BASE_DIR . "/storage/temp/" . $data["directory"] . "/" . $data["filename"];
        $old_dir_path = BASE_DIR . "/storage/temp/" . $data["directory"];
        $new_dir_path = BASE_DIR . "/storage/logos/clients/" . $data["directory"];

        //validation: file exists
        if (!file_exists($file_path)) {
            Log::error("the logo file could not be found", ['process' => '[attachments-client-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path] ?? '');
            return false;
        }

        //check if the file is an image
        if (!is_array(@getimagesize($file_path))) {
            Log::error("the logo file is not an image", ['process' => '[attachments-client-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path] ?? '');
            return false;
        }

        //move directory
        File::moveDirectory($old_dir_path, $new_dir_path, true);

        //return
        return true;
    }

    /**
     * Process a file attachement as follows:
     *   1. Check that the folder and file exist in the 'temp' directory
     *   2. check that the file is an image
     *   3. move the files to the logo directory
     *   4. update the users record
     * :pa
     * @return bool process outcome
     */
    public function processAppLogo($data = []) {

        //sanity
        if ($data["directory"] == '' || $data["filename"] == '' || !in_array($data["logo_size"], ['small', 'large'])) {
            Log::error("required information is missing (directory or filename)", ['process' => '[attachments-app-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'data' => $data] ?? '');
            return false;
        }

        //path to this directory in the temp folder
        $uploaded_file = BASE_DIR . "/storage/temp/" . $data["directory"] . "/" . $data["filename"];
        $new_file = BASE_DIR . "/storage/logos/app/" . $data["filename"];

        if ($data["logo_size"] == 'large') {
            $current_file = BASE_DIR . "/storage/logos/app/" . config('system.settings_system_logo_large_name');
        } else {
            $current_file = BASE_DIR . "/storage/logos/app/" . config('system.settings_system_logo_small_name');
        }

        //validation: file exists
        if (!file_exists($uploaded_file)) {
            Log::error("the logo file could not be found", ['process' => '[attachments-app-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path] ?? '');
            return false;
        }

        //check if the file is an image
        if (!is_array(@getimagesize($uploaded_file))) {
            Log::error("the logo file is not an image", ['process' => '[attachments-app-logo]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'path' => $file_path] ?? '');
            return false;
        }

        //delete the old logo
        @unlink($current_file);

        //move the file
        @rename($uploaded_file, $new_file);

        return true;
    }

}