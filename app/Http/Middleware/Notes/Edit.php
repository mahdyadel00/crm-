<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [edit] precheck processes for notes
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Notes;
use App\Permissions\NotePermissions;
use Closure;
use Log;

class Edit {

    /**
     * The note permisson repository instance.
     */
    protected $notepermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(NotePermissions $notepermissons) {

        $this->notepermissons = $notepermissons;
    }

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] notes
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //basic validation
        if (!$note = \App\Models\Note::Where('note_id', request()->route('note'))->first()) {
            Log::error("note could not be found", ['process' => '[permissions][notes][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'note' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'note id' => $note_id ?? '']);
            abort(409, __('lang.note_not_found'));
        }

        //all
        if ($this->notepermissons->check('edit-delete', $note)) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][notes][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'note' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
        abort(403);
    }
}
