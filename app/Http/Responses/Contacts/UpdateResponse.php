<?php

/** --------------------------------------------------------------------------------
 * This classes renders the response for the [update] process for the contacts
 * controller
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Responses\Contacts;
use Illuminate\Contracts\Support\Responsable;

class UpdateResponse implements Responsable {

    private $payload;

    public function __construct($payload = array()) {
        $this->payload = $payload;
    }

    /**
     * render the view for team members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {

        //set all data to arrays
        foreach ($this->payload as $key => $value) {
            $$key = $value;
        }

        //replace the row of this record
        $html = view('pages/contacts/components/table/ajax', compact('contacts'))->render();
        $jsondata['dom_html'][] = array(
            'selector' => "#contact_" . $contacts->first()->id,
            'action' => 'replace-with',
            'value' => $html);

        //for own profile, replace user name in top nav
        if ($user->id == auth()->id()) {
            $jsondata['dom_html'][] = array(
                'selector' => "#topnav_username",
                'action' => 'replace',
                'value' => safestr($user->first_name));
            $jsondata['dom_html'][] = array(
                'selector' => "#topnav_dropdown_full_name",
                'action' => 'replace',
                'value' => safestr($user->full_name));
            $jsondata['dom_html'][] = array(
                'selector' => "#topnav_dropdown_email",
                'action' => 'replace',
                'value' => safestr($user->email));
        }

        //reset original owner row
        if (request('account_owner') == 'on') {
            $contacts = $original_owner;
            $html = view('pages/contacts/components/table/ajax', compact('contacts'))->render();
            $jsondata['dom_html'][] = array(
                'selector' => "#contact_" . $contacts->first()->id,
                'action' => 'replace-with',
                'value' => $html);
        }

        //close modal
        $jsondata['dom_visibility'][] = array('selector' => '#commonModal', 'action' => 'close-modal');

        //notice
        $jsondata['notification'] = array('type' => 'success', 'value' => __('lang.request_has_been_completed'));

        //response
        return response()->json($jsondata);

    }

}
