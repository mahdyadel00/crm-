<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for estimates settings
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers\Settings;
use App\Http\Controllers\Controller;
use App\Http\Responses\Settings\Invoices\IndexResponse;
use App\Http\Responses\Settings\Invoices\UpdateResponse;
use App\Repositories\SettingsRepository;
use DB;
use Illuminate\Http\Request;
use Validator;

class Invoices extends Controller {

    /**
     * The settings repository instance.
     */
    protected $settingsrepo;

    public function __construct(SettingsRepository $settingsrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        //settings general
        $this->middleware('settingsMiddlewareIndex');

        $this->settingsrepo = $settingsrepo;

    }

    /**
     * Display general settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        //crumbs, page data & stats
        $page = $this->pageSettings();

        $settings = \App\Models\Settings::find(1);

        $query = DB::select("SHOW TABLE STATUS LIKE 'invoices'");
        $next_id = $query[0]->Auto_increment;

        //reponse payload
        $payload = [
            'page' => $page,
            'settings' => $settings,
            'next_id' => $next_id,
        ];

        //show the view
        return new IndexResponse($payload);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update() {

        //validate (add specific error messages, per field name, in validation.lang)
        $validator = Validator::make(request()->all(), [
            'settings_invoices_recurring_grace_period' => 'required',
        ]);

        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();
            $messages = '';
            foreach ($errors->all() as $message) {
                $messages .= "<li>$message</li>";
            }

            abort(409, $messages);
        }

        //are we updating next ID
        if (request()->filled('next_id')) {
            if (request('next_id') != request('next_id_current')) {
                DB::select("ALTER TABLE invoices AUTO_INCREMENT = " . request('next_id'));
            }
        }

        //update
        if (!$this->settingsrepo->updateInvoiceSettings()) {
            abort(409);
        }

        //reponse payload
        $payload = [];

        //generate a response
        return new UpdateResponse($payload);
    }
    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        $page = [
            'crumbs' => [
                __('lang.settings'),
                __('lang.sales'),
                __('lang.invoices'),
            ],
            'crumbs_special_class' => 'main-pages-crumbs',
            'page' => 'settings',
            'meta_title' => __('lang.settings'),
            'heading' => __('lang.settings'),
        ];
        return $page;
    }

}
