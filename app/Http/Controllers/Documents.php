<?php

/** --------------------------------------------------------------------------------
 * Editing of Proposals and Contracts
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\Documents\UpdateDetailsResponse;
use App\Http\Responses\Documents\UpdateHeroResponse;
use App\Repositories\ContractRepository;
use App\Repositories\FileRepository;
use App\Repositories\ProposalRepository;
use Illuminate\Validation\Rule;
use Validator;

class Documents extends Controller {

    /**
     * The repository instance.
     */
    protected $proposalrepo;
    protected $contractrepo;

    public function __construct(ProposalRepository $proposalrepo, ContractRepository $contractrepo) {

        //parent
        parent::__construct();

        $this->contractrepo = $contractrepo;
        $this->proposalrepo = $proposalrepo;

        //authenticated
        $this->middleware('auth');

        $this->middleware('documentsMiddlewareEdit')->only(
            [
                'updateHero',
                'updateDetails',
                'updateBody',
            ]);

    }

    /**
     * Update the hero header
     *
     * @return \Illuminate\Http\Response
     */
    public function updateHero(FileRepository $filerepo, $id) {

        //get the document
        if (request('doc_type') == 'proposal') {
            $document = \App\Models\Proposal::Where('doc_id', $id)->first();
        }
        if (request('doc_type') == 'contract') {
            $document = \App\Models\Contract::Where('doc_id', $id)->first();
        }

        //update record
        $document->doc_heading = request('doc_heading');
        $document->doc_title = request('doc_title');
        $document->doc_heading_color = request('doc_heading_color');
        $document->doc_title_color = request('doc_title_color');
        $document->save();

        //attach new hero image
        if (request()->filled('image_filename') && request()->filled('image_directory')) {
            if ($filerepo->processUpload([
                'directory' => request('image_directory'),
                'filename' => request('image_filename'),
            ])) {
                $document->doc_hero_direcory = request('image_directory');
                $document->doc_hero_filename = request('image_filename');
                $document->doc_hero_updated = 'yes';
                $document->save();
            }
        }

        //get refreshed document (proposal)
        if (request('doc_type') == 'proposal') {
            $documents = $this->proposalrepo->search($id);
            $document = $documents->first();
        }

        //get refreshed document (contract)
        if (request('doc_type') == 'contract') {
            $documents = $this->contractrepo->search($id);
            $document = $documents->first();
        }

        //payload
        $payload = [
            'document' => $document,
            'mode' => 'editing',
        ];

        //return view
        return new UpdateHeroResponse($payload);

    }

    /**
     * show the form to update a resource
     *
     * @return \Illuminate\Http\Response
     */
    public function updateDetails($id) {

        //get the document
        if (request('doc_type') == 'proposal') {
            $document = \App\Models\Proposal::Where('doc_id', $id)->first();
        }
        if (request('doc_type') == 'contract') {
            $document = \App\Models\Contract::Where('doc_id', $id)->first();
        }

        //custom error messages
        if (request('doc_type') == 'proposal') {
            $messages = [
                'doc_date_start.required' => __('lang.proposal_date') . ' - ' . __('lang.is_required'),
                'doc_categoryid.required' => __('lang.category') . ' - ' . __('lang.is_required'),
            ];
        } else {
            $messages = [
                'doc_date_start.required' => __('lang.contract_start_date') . ' - ' . __('lang.is_required'),
                'doc_categoryid.required' => __('lang.category') . ' - ' . __('lang.is_required'),
            ];
        }

        //validate
        $validator = Validator::make(request()->all(), [
            'doc_date_start' => [
                'required',
                'date',
            ],
            'doc_date_end' => [
                'nullable',
                'date',
            ],
            'doc_categoryid' => [
                'required',
                Rule::exists('categories', 'category_id'),
            ],
        ], $messages);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }
            abort(409, $messages);
        }

        //old info
        $old_status = $document->doc_status;

        //update record
        $document->doc_date_start = request('doc_date_start');
        $document->doc_date_end = request('doc_date_end');
        $document->doc_creatorid = request('doc_creatorid');
        $document->doc_categoryid = request('doc_categoryid');
        $document->doc_status = request('doc_status');
        $document->save();

        //get refreshed document (proposal)
        if (request('doc_type') == 'proposal') {
            $documents = $this->proposalrepo->search($id);
            $document = $documents->first();
        }

        //get refreshed document (contract)
        if (request('doc_type') == 'contract') {
            $documents = $this->contractrepo->search($id);
            $document = $documents->first();
        }

        //payload
        $payload = [
            'document' => $document,
            'mode' => 'editing',
        ];

        //return view
        return new UpdateDetailsResponse($payload);

    }

    /**
     * Update the rich text body
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBody($id) {

        //get the document
        if (request('doc_type') == 'proposal') {
            $document = \App\Models\Proposal::Where('doc_id', $id)->first();
        }
        if (request('doc_type') == 'contract') {
            $document = \App\Models\Contract::Where('doc_id', $id)->first();
        }

        //update record
        $document->doc_body = request('doc_body');
        $document->save();

        return response()->json(array(
            'notification' => [
                'type' => 'success',
                'value' => __('lang.request_has_been_completed'),
            ],
            'skip_dom_reset' => true,
        ));

    }

}