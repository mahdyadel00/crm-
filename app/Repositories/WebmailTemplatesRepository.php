<?php

/** --------------------------------------------------------------------------------
 * This repository class manages all the data absctration for templates
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Repositories;

use App\Models\WebmailTemplate;
use Illuminate\Http\Request;
use Log;

class WebmailTemplatesRepository {

    /**
     * The leads repository instance.
     */
    protected $template;

    /**
     * Inject dependecies
     */
    public function __construct(WebmailTemplate $template) {
        $this->template = $template;
    }

    /**
     * Search model
     * @param int $id optional for getting a single, specified record
     * @return object foos collection
     */
    public function search($id = '') {

        $templates = $this->template->newQuery();

        // all client fields
        $templates->selectRaw('*');

        //joins
        $templates->leftJoin('users', 'users.id', '=', 'webmail_templates.webmail_template_creatorid');

        //default where
        $templates->whereRaw("1 = 1");

        if (is_numeric($id)) {
            $templates->where('webmail_template_id', $id);
        }

        //default sorting
        $templates->orderBy('webmail_template_name', 'asc');

        // Get the results and return them.
        return $templates->paginate(1000);
    }

    /**
     * Create a new record
     * @return mixed int|bool
     */
    public function create() {

        //save new user
        $template = new $this->template;

        //data
        $template->webmail_template_name = request('webmail_template_name');
        $template->webmail_template_creatorid = auth()->id();
        $template->webmail_template_body = request('webmail_template_body');

        //save and return id
        if ($template->save()) {
            return $template->webmail_template_id;
        } else {
            Log::error("unable to create record - database error", ['process' => '[WebmailTemplateRepository]', config('app.debug_ref'), 'function' => __function__, 'file' => basename(__FILE__), 'line' => __line__, 'path' => __file__]);
            return false;
        }
    }

}