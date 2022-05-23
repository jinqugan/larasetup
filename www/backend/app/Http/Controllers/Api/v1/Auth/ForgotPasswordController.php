<?php
/**
 * Controller : ForgotPasswordController.
 *
 * This file used to handle forgot password
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */
 
namespace App\Http\Controllers\Api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordForgotOtpRequest;
use App\Http\Requests\Auth\PasswordForgotOtpResendRequest;
use App\Http\Requests\Auth\PasswordForgotOtpVerifyRequest;
use App\Http\Requests\Auth\PasswordForgotResetRequest;
use App\Repositories\User\UserInterface;
use App\Http\Resources\User\OneTimePasswordResource;

class ForgotPasswordController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * send sms otp to phone/email
     *
     * @param App\Http\Requests\Auth\PasswordForgotOtpRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sendOtp(PasswordForgotOtpRequest $request) {
        $request['otp'] = $this->generateOtp();

        $userOtp = $this->user->requestOtp($request);

        return (new OneTimePasswordResource($userOtp))
        ->message(trans('passwords.otp_sent_success'));
    }

    /**
     * Sms otp to phone/email
     *
     * @param App\Http\Requests\Auth\PasswordForgotOtpResendRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resendOtp(PasswordForgotOtpResendRequest $request) {
        $request['otp'] = $this->generateOtp();

        $userOtp = $this->user->resendOtp($request);

        // if ($request['user_type'] === 'email') {
        //     $user = $this->user->getUserByEmail($request['username']);

        //     Notification::send($user, new ResetPasswordNotification($userOtp));
        // }

        return (new OneTimePasswordResource($userOtp))
        ->message(trans('passwords.otp_resent_success'));
    }

    /**
     * verify user otp
     *
     * @param App\Http\Requests\Auth\PasswordForgotOtpVerifyRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyOtp(PasswordForgotOtpVerifyRequest $request) {
        $request['otp'] = $this->generateOtp();

        $userOtp = $this->user->verifyOtp($request);

        return (new OneTimePasswordResource($userOtp))
        ->message(trans('passwords.otp_verify_success'));
    }

    /**
     * User reset password
     *
     * @param App\Http\Requests\Auth\PasswordForgotResetRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(PasswordForgotResetRequest $request)
    {
        $userOtp = $this->user->getOtpBySessionId($request['session_id']);

        $this->user->resetPassword($userOtp->user_id, $request['password']);
        $this->user->deleteOtpById($userOtp->id);

        return (new OneTimePasswordResource(false))
        ->message(trans('passwords.reset_password_success'));
    }
}
