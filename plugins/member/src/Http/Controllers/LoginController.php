<?php

namespace Botble\Member\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\ACL\Traits\LogoutGuardTrait;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use SeoHelper;
use Theme;
use URL;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogoutGuardTrait {
        LogoutGuardTrait::logout insteadof AuthenticatesUsers;
    }

    use AuthenticatesUsers {
        attemptLogin as baseAttemptLogin;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     * @author Sang Nguyen
     */
    public function __construct()
    {
        $this->middleware('member.guest', ['except' => 'logout']);

        session(['url.intended' => URL::previous()]);
        $this->redirectTo = session()->get('url.intended');
    }

    /**
     * Show the application's login form.
     *
     * @return \Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author Sang Nguyen
     */
    public function showLoginForm()
    {
        SeoHelper::setTitle(__('Login'));
        Theme::asset()->add('member-css', asset('/vendor/core/plugins/member/css/member.css'));
        return Theme::scope('member.login', [], 'plugins.member::themes.login')->render();
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     * @author Sang Nguyen
     */
    protected function guard()
    {
        return Auth::guard('member');
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Validation\ValidationException
     * @author Sang Nguyen
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     * @throws ValidationException
     * @author Sang Nguyen
     */
    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            $member = $this->guard()->getLastAttempted();

            if (!empty($member->confirmed_at)) {
                return $this->baseAttemptLogin($request);
            }

            session([
                'confirmation_user_id' => $member->getKey(),
            ]);

            throw ValidationException::withMessages([
                'confirmation' => [
                    __('plugins.member::member.not_confirmed', [
                        'resend_link' => route('public.member.resend_confirmation')
                    ]),
                ],
            ]);
        }
        return false;
    }
}
