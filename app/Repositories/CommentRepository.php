<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for comments
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\Comment;
use Illuminate\Http\Request;
use Log;

class CommentRepository {

    /**
     * The comments repository instance.
     */
    protected $comments;

    /**
     * Inject dependecies
     */
    public function __construct(Comment $comments) {
        $this->comments = $comments;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object comment collection
     */
    public function search($id = '') {

        //new query
        $comments = $this->comments->newQuery();

        // all client fields
        $comments->selectRaw('*');

        //joins
        $comments->leftJoin('users', 'users.id', '=', 'comments.comment_creatorid');
        $comments->leftJoin('clients', 'clients.client_id', '=', 'comments.comment_clientid');

        //default where
        $comments->whereRaw("1 = 1");

        //limit by id
        if (is_numeric($id)) {
            $comments->where('comment_id', $id);
        }

        //filters: resource type
        if (request()->filled('commentresource_type')) {
            $comments->where('commentresource_type', request('commentresource_type'));
        }

        //filters: resource type
        if (request()->filled('commentresource_id')) {
            $comments->where('commentresource_id', request('commentresource_id'));
        }

        //filter clients
        if (request()->filled('filter_comment_clientid')) {
            $invoices->where('comment_clientid', request('filter_comment_clientid'));
        }

        //default sorting
        $comments->orderBy('comment_id', 'desc');

        return $comments->paginate(100000);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $comment = new $this->comments;

        //data
        $comment->comment_creatorid = auth()->id();
        $comment->comment_text = request('comment_text');
        $comment->commentresource_type = request('commentresource_type');
        $comment->commentresource_id = request('commentresource_id');

        //save and return id
        if ($comment->save()) {
            return $comment->comment_id;
        } else {
            Log::error("saving record failed - database error", ['process' => '[CommentRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }
}