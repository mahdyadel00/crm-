<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for users
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\User\CommonResponse;
use App\Http\Responses\User\EditAvatarResponse;
use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class User extends Controller {

    /**
     * The user repository instance.
     */
    protected $userrepo;

    public function __construct(UserRepository $userrepo) {

        //parent
        parent::__construct();

        //authenticated
        $this->middleware('auth');

        $this->userrepo = $userrepo;
    }

    /**
     * Show the form for editing the specified users avatar
     * @return \Illuminate\Http\Response
     */
    public function avatar() {

        //reponse payload
        $payload = [];

        //response
        return new EditAvatarResponse($payload);
    }

    /**
     * Update the specified user in storage.
     * @param object AttachmentRepository instance of the repository
     * @param int $id user id
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(AttachmentRepository $attachmentrepo) {

        //validate input
        $data = [
            'directory' => request('avatar_directory'),
            'filename' => request('avatar_filename'),
        ];

        //process and save to db
        if (!$attachmentrepo->processAvatar($data)) {
            abort(409);
        }

        //update avatar
        if (!$this->userrepo->updateAvatar(auth()->id())) {
            abort(409);
        }

        //reponse payload
        $payload = [
            'type' => 'update-avatar',
            'img_source' => url('/storage/avatars/' . request('avatar_directory') . '/' . request('avatar_filename')),
        ];

        //generate a response
        return new CommonResponse($payload);
    }

    /**
     * Display the update password form
     * @return \Illuminate\Http\Response
     */
    public function updatePassword() {

        $payload = [
            'type' => 'update-password-form',
        ];

        //show the form
        return new CommonResponse($payload);
    }

    /**
     * Update authenticated users password
     * @return \Illuminate\Http\Response
     */
    public function updatePasswordAction() {

        //default - own password
        $user = \App\Models\User::Where('id', auth()->id())->first();

        //team member, updating a contact
        if (request()->filled('contact_id') && auth()->user()->is_team) {
            $user = \App\Models\User::Where('id', request('contact_id'))->first();
        }

        //updating a team members password
        if (request()->filled('team_id')) {
            $user = \App\Models\User::Where('id', request('team_id'))->first();
            //check permissions
            if (!runtimeTeamPermissionEdit($user)) {
                abort(403);
            }
        }

        //validate (add specific error messages, per field name, in validation.lang)
        $validator = Validator::make(request()->all(), [
            'password' => 'required|confirmed|min:6',
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

        //update database
        $user->password = Hash::make(request('password'));
        $user->force_password_change = 'no';
        $user->save();

        //reponse payload
        $payload = [
            'type' => 'update-password-action',
        ];

        //generate a response
        return new CommonResponse($payload);

    }

    /**
     * Show the form for changing theme
     * @return blade view | ajax view
     */
    public function updateTheme() {

        //page
        $html = view('pages/user/modals/change-theme')->render();
        $jsondata['dom_html'][] = [
            'selector' => '#commonModalBody',
            'action' => 'replace',
            'value' => $html,
        ];

        //postrun
        $jsondata['postrun_functions'][] = [
            'value' => 'NXUpdateUserTheme',
        ];

        //render
        return response()->json($jsondata);

    }

    /**
     * update the users theme
     *
     * @return \Illuminate\Http\Response
     */
    public function updateThemeAction() {

        //valdate
        if (!request()->filled('pref_theme')) {
            abort(409, __('lang.theme') . ' - ' . __('lang.is_required'));
        }

        //update users theme
        auth()->user()->pref_theme = request('pref_theme');
        auth()->user()->save();

        //redirect to packages page
        $jsondata['redirect_url'] = url("home");

        //ajax response
        return response()->json($jsondata);

    }

    /**
     * Update users language
     * @return \Illuminate\Http\Response
     */
    public function updateLanguage() {

        //get this user
        $user = \App\Models\User::Where('id', auth()->id())->first();

        //get the language
        $language = strtolower(request('language'));

        //validate
        if (!request()->filled('language') || config('system.settings_system_language_allow_users_to_change') != 'yes') {
            //failed
            abort(409);
        }

        //check language exists
        if (!file_exists(resource_path("lang/$language"))) {
            abort(409);
        }

        //save settings
        $user->pref_language = $language;
        $user->save();

        //change language
        \App::setLocale($language);

        $payload = [
            'type' => 'update-language',
        ];

        //show the form
        return new CommonResponse($payload);

    }

    /**
     * Display the update notiffication settings form
     * @return \Illuminate\Http\Response
     */
    public function updateNotifications() {

        $payload = [
            'type' => 'update-notifications-form',
        ];

        //show the form
        return new CommonResponse($payload);
    }

    /**
     * Update authenticated users notifications settings
     * @return \Illuminate\Http\Response
     */
    public function updateNotificationsAction() {

        $user = \App\Models\User::Where('id', auth()->id())->first();

        //common settings
        $user->notifications_system = request('notifications_system');
        $user->notifications_projects_activity = request('notifications_projects_activity');
        $user->notifications_tasks_activity = request('notifications_tasks_activity');
        $user->notifications_tickets_activity = request('notifications_tickets_activity');

        //update team user
        if (auth()->user()->is_team) {
            $user->notifications_new_assignement = request('notifications_new_assignement');
            $user->notifications_billing_activity = request('notifications_billing_activity');
            $user->notifications_leads_activity = request('notifications_leads_activity');
        }

        //update client user
        if (auth()->user()->is_client) {
            $user->notifications_new_project = request('notifications_new_project');
        }

        //save changes
        $user->save();

        //reponse payload
        $payload = [
            'type' => 'update-notifications-action',
        ];

        //generate a response
        return new CommonResponse($payload);

    }

}