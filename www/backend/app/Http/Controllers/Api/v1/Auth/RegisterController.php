<?php
/**
 * Controller : RegisterController.
 *
 * This file used to handle user registration
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\RegisterOtpRequest;
use App\Http\Requests\Auth\RegisterOtpVerifyRequest;
use App\Http\Requests\Auth\RegisterOtpResendRequest;
use App\Repositories\User\UserInterface;
use App\Http\Resources\User\OneTimePasswordResource;
use App\Http\Resources\User\UserResource;
use App\Traits\UserTrait;

class RegisterController extends Controller
{
    use UserTrait;

    protected $user;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function action(RegisterRequest $request)
    {
        $userOtp = $this->user->getOtpBySessionId($request['session_id']);

        $request['username'] = $userOtp['username'];
        $request['old_otp_id'] = $userOtp['id'];

        $userType = $this->usernameBelongTo($request['username']);

        if ($userType === 'mobileno') {
            $request['mobileno'] = $request['username'];
        } elseif ($userType === 'email') {
            $request['email'] = $request['username'];
        }

        $user = $this->user->userRegister($request);

        $deviceData = [
            'user_id' => $user->id,
            'platform' => $request['source'],
            'device_id' => $request['device_id'] ?? null
        ];

        $this->user->findOrNewDevice($deviceData);

        return (new UserResource($user))
        ->message(trans('register.success_register_account'))
        ->additional(['meta' => $this->requestAccessToken($user->id, $request['password'])]);
    }

    public function requestOtp(RegisterOtpRequest $request)
    {
        $request['otp'] = $this->generateOtp();

        $registerOtp = $this->user->requestOtp($request);

        return (new OneTimePasswordResource($registerOtp))
        ->message(trans('register.success_request_otp'));
    }

    public function resendOtp(RegisterOtpResendRequest $request)
    {
        $request['otp'] = $this->generateOtp();

        $registerOtp = $this->user->resendOtp($request);

        return (new OneTimePasswordResource($registerOtp))
        ->message(trans('register.success_resend_otp'));
    }

    public function verifyOtp(RegisterOtpVerifyRequest $request)
    {
        $verifyOtp = $this->user->verifyOtp($request);

        return (new OneTimePasswordResource($verifyOtp))
        ->message(trans('register.success_verify_otp'));
    }
}
