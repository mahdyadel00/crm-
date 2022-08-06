<?php

/** --------------------------------------------------------------------------------
 * This middleware class handles [destroy] precheck processes for comments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Middleware\Comments;
use App\Permissions\CommentPermissions;
use Closure;
use Log;

class Destroy {

    /**
     * The comment permisson repository instance.
     */
    protected $commentpermissons;

    /**
     * Inject any dependencies here
     *
     */
    public function __construct(CommentPermissions $commentpermissons) {

        $this->commentpermissons = $commentpermissons;
    }

    /**
     * This middleware does the following
     *   2. checks users permissions to [view] comments
     *   3. modifies the request object as needed
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //basic validation
        if (!$comment = \App\Models\Comment::Where('comment_id', request()->route('comment'))->first()) {
            Log::error("comment could not be found", ['process' => '[permissions][comments][edit]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'comment_id' => request()->route('comment')]);
            abort(409, __('lang.comment_not_found'));
        }

        //all
        if ($this->commentpermissons->check('delete', $comment)) {
            return $next($request);
        }

        //permission denied
        Log::error("permission denied", ['process' => '[permissions][comments][create]', 'ref' => config('app.debug_ref'), 'function' => __function__, 'comment' => basename(__FILE__), 'line' => __line__, 'path' => __file__, 'comment_id' => request()->route('comment'), 'user_id' => auth()->id()]);
        abort(403);
    }
}
