<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for file uploads
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;
use Intervention\Image\Exception\NotReadableException;

class Fileupload extends Controller {

    //contruct
    public function __construct() {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth')->except('saveWebForm');
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function save(Request $request) {

        //Sep 2021 - Block all these for security
        $excluded = [
            '.php',
            '.exe',
            '.html',
            '.htm',
            '.htaccess',
            '.pl',
            '.cgi',
            '.js',
            '.script',
            '.sh',
            '.asp',
            '.ph3',
            '.php4',
            '.php3',
            '.php5',
            '.phtm',
            '.phtml',
            '.sql',
            '.bak',
            '.config',
            '.fla',
            '.inc',
            '.log',
            '.ini',
            '.dist',
        ];

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $filename = $file->getClientOriginalName();

            //validate if file type if allowed
            $extension = $file->getClientOriginalExtension();
            if (is_array(config('settings.disallowed_file_types'))) {
                if (in_array(".$extension", $excluded)) {
                    abort(409, __('lang.file_type_not_allowed'));
                }
            }

            //filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //thumb path
            $thumb_name = generateThumbnailName($filename);
            $thumb_path = BASE_DIR . "/storage/temp/$directory/$thumb_name";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            if (is_array(@getimagesize($file_path))) {
                try {
                    $img = Image::make($file_path)->resize(null, 90, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumb_path);
                } catch (NotReadableException $e) {
                    $message = $e->getMessage();
                    Log::error("[Image Library] failed to create uplaoded image thumbnail. Image type is not supported on this server", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $message]);
                    abort(409, __('lang.image_file_type_not_supported'));
                }
            }

            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function saveWebForm(Request $request) {

        //Sep 2021 - Block all these for security
        $excluded = [
            '.php',
            '.exe',
            '.html',
            '.htm',
            '.htaccess',
            '.pl',
            '.cgi',
            '.js',
            '.script',
            '.sh',
            '.asp',
            '.ph3',
            '.php4',
            '.php3',
            '.php5',
            '.phtm',
            '.phtml',
            '.sql',
            '.bak',
            '.config',
            '.fla',
            '.inc',
            '.log',
            '.ini',
            '.dist',
        ];

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name
            $filename = $file->getClientOriginalName();

            //validate if file type if allowed
            $extension = $file->getClientOriginalExtension();
            if (is_array(config('settings.disallowed_file_types'))) {
                if (in_array(".$extension", $excluded)) {
                    abort(409, __('lang.file_type_not_allowed'));
                }
            }

            //filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //thumb path
            $thumb_name = generateThumbnailName($filename);
            $thumb_path = BASE_DIR . "/storage/temp/$directory/$thumb_name";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            if (is_array(@getimagesize($file_path))) {
                try {
                    $img = Image::make($file_path)->resize(null, 90, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    $img->save($thumb_path);
                } catch (NotReadableException $e) {
                    $message = $e->getMessage();
                    Log::error("[Image Library] failed to create uplaoded image thumbnail. Image type is not supported on this server", ['process' => '[permissions]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'error_message' => $message]);
                    abort(409, __('lang.image_file_type_not_supported'));
                }
            }

            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * upload a general image, with no size restrictions.
     * [NOTE] do not add any restrictions here because its used by other processes
     *        that are not expecting a restriction like file size etc.
     * @param object Request instance of the request object
     */
    public function saveGeneralImage(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //files details
            $extension = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/jp2'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.allowed_avatar_file_type'));
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function saveLogo(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename
            $filename = "logo.$extension";

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check dims (min width 100px min height 100px)
            if ($imagedata[0] < 80 || $imagedata[1] < 80 || $imagedata[0] > 500 || $imagedata[1] > 500) {
                abort(409, __('lang.image_dimensions_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.allowed_avatar_file_type'));
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function saveAvatar(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename
            $filename = "avatar.$extension";

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check dims (min width 100px min height 100px)
            if ($imagedata[0] < 80 || $imagedata[1] < 80 || $imagedata[0] > 500 || $imagedata[1] > 500) {
                abort(409, __('lang.image_dimensions_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.allowed_avatar_file_type'));
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

    /**
     * save an uploaded file (logo)
     * @param object Request instance of the request object
     */
    public function saveAppLogo(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename
            if (request('logo_size') == 'large') {
                $filename = "logo.$extension";
            } else {
                $filename = "logo-small.$extension";
            }

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.allowed_avatar_file_type'));
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
                'filename' => $filename,
                'logo_size' => request('logo_size'),
            ]);
        }
    }

    /**
     * save an uploaded file from tinymce editor
     */
    public function saveTinyMCEImage(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename (make it a safe file name)
            $filename = "img.$extension";

            //new filepath
            $file_path = BASE_DIR . "/storage/files/$directory/$filename";

            //create directory
            Storage::makeDirectory("files/$directory");

            //save file to directory
            Storage::putFileAs("files/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/jp2'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //success - send back a response
            return response()->json([
                //'location' => url("/storage/files/$directory/$filename"),
                'location' => "storage/files/$directory/$filename",

            ]);
        }
    }

    /**
     * Upload any file into the temp folder
     * @param object Request instance of the request object
     */
    public function uploadImportFiles(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file name & extension
            $extension = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();
            $filesize = $file->getSize();

            //validate
            $valid_extensions = ['csv', 'xlsx'];

            if (!in_array($extension, $valid_extensions)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'filename' => $filename,
                'extension' => $extension,
                'filesize' => humanFileSize($filesize),
            ]);
        }
    }

    /**
     * save an uploaded file
     * @param object Request instance of the request object
     */
    public function uploadCoverImage(Request $request) {

        //save the file in its own folder in the temp folder
        if ($file = $request->file('file')) {

            //unique file id & directory name
            $uniqueid = Str::random(40);
            $directory = $uniqueid;

            //original file extension
            $extension = $file->getClientOriginalExtension();

            //new filename
            $filename = "cover-image.$extension";

            //new filepath
            $file_path = BASE_DIR . "/storage/temp/$directory/$filename";

            //create directory
            Storage::makeDirectory("temp/$directory");

            //save file to directory
            Storage::putFileAs("temp/$directory", $file, $filename);

            //if the file type is an image, create a thumb by default
            $imagedata = @getimagesize($file_path);
            if (!is_array($imagedata)) {
                abort(409, __('lang.file_type_not_allowed'));
            }

            //check dims (min width 100px min height 100px)
            if ($imagedata[0] < 400 || $imagedata[1] < 170) {
                abort(409, __('lang.image_size_wrong_cover_image') . ' (400px X 170px)');
            }

            //check if image 'mime type' is jpeg|png
            $allowed_mime = ['image/jpeg', 'image/png'];
            if (!in_array($imagedata['mime'], $allowed_mime)) {
                abort(409, __('lang.file_type_not_allowed') . ' (' . __('lang.jpg_png_only') . ')');
            }

            //success - send back a response
            return response()->json([
                'success' => true,
                'uniqueid' => $uniqueid,
                'directory' => $directory,
                'filename' => $filename,
            ]);
        }
    }

}