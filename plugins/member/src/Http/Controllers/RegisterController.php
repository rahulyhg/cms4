<?php

namespace Botble\Member\Http\Controllers;

use App\Http\Controllers\Controller;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Member\Models\Member;
use Botble\Member\Repositories\Interfaces\MemberInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SeoHelper;
use Theme;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     * @author Sang Nguyen
     */
    public function __construct()
    {
        $this->middleware('member.guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     * @author Sang Nguyen
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:members',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return Member
     * @author Sang Nguyen
     */
    protected function create(array $data)
    {
        return Member::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    /**
     * Show the application registration form.
     *
     * @return \Response
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @author Sang Nguyen
     */
    public function showRegistrationForm()
    {
        SeoHelper::setTitle(__('Register'));
        Theme::asset()->add('member-css', asset('/vendor/core/plugins/member/css/member.css'));
        return Theme::scope('member.register', [], 'plugins.member::themes.register')->render();
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     * @author Sang Nguyen
     */
    protected function guard()
    {
        return Auth::guard('member');
    }

    /**
     * Confirm a user with a given confirmation code.
     *
     * @param $confirmation_code
     * @param BaseHttpResponse $response
     * @param MemberInterface $memberRepository
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function confirm($confirmation_code, BaseHttpResponse $response, MemberInterface $memberRepository)
    {
        $member = $memberRepository->getFirstBy(['confirmation_code' => $confirmation_code]);

        if (!$member) {
            abort(404);
        }

        $member->confirmation_code = null;
        $member->confirmed_at = now();
        $member->save();

        return $response
            ->setNextUrl(route('public.member.login'))
            ->setMessage(__('plugins.member::member.confirmation_successful'));
    }

    /**
     * Resend a confirmation code to a user.
     *
     * @param  \Illuminate\Http\Request $request
     * @param MemberInterface $memberRepository
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function resendConfirmation(Request $request, MemberInterface $memberRepository, BaseHttpResponse $response)
    {
        $member = $memberRepository->findOrFail($request->session()->pull('confirmation_user_id'));

        $this->sendConfirmationToUser($member);

        return $response
            ->setNextUrl(route('public.member.login'))
            ->setMessage(__('plugins.member::member.confirmation_resent'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function register(Request $request, BaseHttpResponse $response)
    {
        $this->validator($request->input())->validate();

        event(new Registered($member = $this->create($request->input())));

        $this->sendConfirmationToUser($member);

        return $this->registered($request, $member)
            ?: $response->setNextUrl($this->redirectPath())->setMessage(__('plugins.member::member.confirmation_info'));
    }

    /**
     * Send the confirmation code to a user.
     *
     * @param Member $member
     * @author Sang Nguyen
     */
    protected function sendConfirmationToUser($member)
    {
        // Create the confirmation code
        $member->confirmation_code = str_random(25);
        $member->save();

        // Notify the user
        $notification = app(config('plugins.member.general.notification'));
        $member->notify($notification);
    }
}
