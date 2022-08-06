<?php

/** --------------------------------------------------------------------------------
 * This controller manages all the business logic for authentication
 *
 * @package    Grow CRM
 * @author     NextLoop
 *----------------------------------------------------------------------------------*/

namespace App\Http\Controllers;

use App\Http\Responses\Authentication\AuthenticateResponse;
use App\Mail\ForgotPassword;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Validator;

class Authenticate extends Controller {

    /**
     * The user repository instance.
     */
    protected $userrepo;

    /**
     * The client instance.
     */
    protected $clientrepo;

    public function __construct(
        UserRepository $userrepo,
        ClientRepository $clientrepo) {

        //parent
        parent::__construct();

        //vars
        $this->userrepo = $userrepo;
        $this->clientrepo = $clientrepo;

        //guest
        $this->middleware('guest')->except([
            'updatePassword',
        ]);

        //logged in
        $this->middleware('auth')->only([
            'updatePassword',
        ]);

        //general middleware
        $this->middleware('authenticationMiddlewareGeneral');
    }

    /**
     * Display the login form
     * @return \Illuminate\Http\Response
     */
    public function logIn() {
        //show login page
        return view('pages/authentication/login');
    }

    /**
     * Display the signup form
     * @return \Illuminate\Http\Response
     */
    public function signUp() {

        if (config('system.settings_clients_registration') == 'disabled') {
            abort(409, __('lang.this_feature_is_unavailable'));
        }
        //show login page
        return view('pages/authentication/signup');
    }

    /**
     * Display the forgot password form
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword() {
        //show login page
        return view('pages/authentication/forgotpassword');
    }

    /**
     * Display the reset password form
     * @return \Illuminate\Http\Response
     */
    public function resetPassword() {

        //1 hour expiry
        $expiry = \Carbon\Carbon::now()->subHours(1);

        //validate code
        if (\App\Models\User::Where('forgot_password_token', request('token'))
            ->where('forgot_password_token_expiry', '>=', $expiry)
            ->doesntExist()) {
            //set flass session
            request()->session()->flash('error-notification-longer', __('lang.url_expired_or_invalid'));
            //redirect
            return redirect('forgotpassword');
        }

        //show login page
        return view('pages/authentication/resetpassword');
    }

    /**
     * process login request
     * @return \Illuminate\Http\Response
     */
    public function logInAction() {

        //get credentials
        $credentials = request()->only('email', 'password');
        $remember = (request('remember_me') == 'on') ? true : false;

        //check credentials
        if (Auth::attempt($credentials, $remember)) {
            //if client - check if account is not suspended
            if (auth()->user()->is_client) {
                if ($client = \App\Models\Client::Where('client_id', auth()->user()->clientid)->first()) {
                    if ($client->client_status != 'active') {
                        abort(409, __('lang.account_has_been_suspended'));
                    }
                } else {
                    abort(409, __('lang.item_not_found'));
                }
            }

            //client are not allowed to login
            if (auth()->user()->is_client && config('system.settings_clients_app_login') != 'enabled') {
                abort(409, __('lang.clients_disabled_login_error'));
            }

            //if account not active
            if (auth()->user()->status != 'active') {
                auth()->logout();
                abort(409, __('lang.account_has_been_suspended'));
            }
        } else {
            //login failed message
            abort(409, __('lang.invalid_login_details'));
        }

        $payload = [
            'type' => request('action'),
        ];

        //show the form
        return new AuthenticateResponse($payload);
    }

    /**
     * process forgot password request
     * @return \Illuminate\Http\Response
     */
    public function forgotPasswordAction() {

        //validation
        if (!$user = \App\Models\User::Where('email', request('email'))->first()) {
            abort(409, __('lang.account_not_found'));
        }

        $code = Str::random(50);

        //update user - set expiry to 3 Hrs
        $user->forgot_password_token = $code;
        $user->forgot_password_token_expiry = \Carbon\Carbon::now()->addHours(3);
        $user->save();

        /** ----------------------------------------------
         * send email [comment
         * ----------------------------------------------*/
        if ($user->type == 'client' && config('system.settings_clients_disable_email_delivery') == 'enabled') {
            abort(409, __('lang.clients_disabled_login_error'));
        } else {
            Mail::to($user->email)->send(new ForgotPassword($user));
        }

        //set flash session
        request()->session()->flash('success-notification-longer', __('lang.password_reset_email_sent'));

        //back to login
        $jsondata['redirect_url'] = url('login');
        return response()->json($jsondata);
    }

    /**
     * process reset password request
     * @return \Illuminate\Http\Response
     */
    public function resetPasswordAction() {

        //1 hour expiry
        $expiry = \Carbon\Carbon::now()->subHours(1);

        $messages = [];

        //validate code
        if (\App\Models\User::Where('forgot_password_token', request('token'))
            ->where('forgot_password_token_expiry', '>=', $expiry)
            ->doesntExist()) {
            //set flass session
            request()->session()->flash('error-notification-longer', __('lang.url_expired_or_invalid'));
            //back to login
            $jsondata['redirect_url'] = url('forgotpassword');
            //redirect
            return response()->json($jsondata);
        }

        //validate password match
        $validator = Validator::make(request()->all(), [
            'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'min:6',
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

        $user = \App\Models\User::Where('forgot_password_token', request('token'))->first();
        $user->password = Hash::make(request('password'));
        $user->forgot_password_token = '';
        $user->save();

        //set flass session
        request()->session()->flash('success-notification-longer', __('lang.password_reset_success'));
        //back to login
        $jsondata['redirect_url'] = url('login');
        return response()->json($jsondata);
    }

    /**
     * process new client signup action
     * @return \Illuminate\Http\Response
     */
    public function signUpAction() {

        //check if the feature is enabled
        if (config('system.settings_clients_registration') == 'disabled') {
            abort(409, __('lang.this_feature_is_unavailable'));
        }

        $messages = [];

        //validate
        $validator = Validator::make(request()->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'client_company_name' => 'required',
            'password' => 'required|confirmed|min:6',
            'email' => 'email|required|unique:users,email',
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

        //create the client
        if (!$client = $this->clientrepo->signUp()) {
            abort(409);
        }

        //create user
        if (!$user = $this->userrepo->signUp($client->client_id)) {
            abort(409);
        }

        //login the user
        $credentials = request()->only('email', 'password');
        $remember = true;
        Auth::attempt($credentials, $remember);

        /** ----------------------------------------------
         * send email to user
         * ----------------------------------------------*/
        $data = [
            'password' => request('password'),
        ];
        $mail = new \App\Mail\UserWelcome($user, $data);
        $mail->build();

        //set flass session
        request()->session()->flash('success-notification-longer', __('lang.welcome_to_dashboard'));

        //redirect to home
        $jsondata['redirect_url'] = url('home');
        return response()->json($jsondata);
    }

    /**
     * basic page setting for this section of the app
     * @param string $section page section (optional)
     * @param array $data any other data (optional)
     * @return array
     */
    private function pageSettings($section = '', $data = []) {

        //Login
        if ($section == 'login') {
            $page = [
                'meta_title' => __('lang.login_to_you_account'),
            ];
        }

        //Signup
        if ($section == 'signup') {
            $page = [
                'meta_title' => __('lang.create_a_new_account'),
            ];
        }

        //Forgot Password
        if ($section == 'forgot-password') {
            $page = [
                'meta_title' => __('lang.forgot_password'),
            ];
        }
        //return
        return $page;
    }

}