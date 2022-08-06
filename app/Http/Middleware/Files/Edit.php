<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for template
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Files;
use App\Permissions\FilePermissions;
use Closure;
use Log;

class Edit {

    /**
     * The file permisson repository instance.
     */
    protected $filepermissions;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(FilePermissions $filepermissions) {

        $this->filepermissions = $filepermissions;
    }

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] files
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //file id
        $file_id = $request->route('file');

        //frontend
        $this->fronteEnd();

        //does the file exist
        if ($file_id == '' || !$file = \App\Models\File::Where('file_id', $file_id)->first()) {
            Log::error("file could not be found", ['process' => '[files][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'file id' => $file_id ?? '']);
            abort(404);
        }

        //permission: does user have permission edit files
        if (auth()->user()->is_team) {
            if ($this->filepermissions->check('edit', $file)) {
                return $next($request);
            }
        }

        //client: does user have permission edit files
        if (auth()->user()->is_client) {
            if ($file->file_clientid == auth()->user()->clientid) {
                return $next($request);
            }
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][files][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }

    /*
     * various frontend and visibility settings
     */
    private function fronteEnd() {

    }
}