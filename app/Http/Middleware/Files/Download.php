<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [download] precheck processes for files
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Files;
use App\Permissions\FilePermissions;
use Closure;
use Log;

class Download {

    /**
     * The file permisson repository instance.
     */
    protected $filepermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(FilePermissions $filepermissons) {

        $this->filepermissons = $filepermissons;
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

        //basic validation
        if (!$file = \App\Models\File::Where('file_uniqueid', request('file_id'))->first()) {
            Log::error("file could not be found", ['process' => '[permissions][files][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'file id' => $file_id ?? '']);
            abort(404);
        }

        //project file
        if ($this->filepermissons->check('download', $file)) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][files][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
