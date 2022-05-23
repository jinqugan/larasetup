<?php
/**
 * Controller : LoginController.
 *
 * This file used to handle user login credential
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserInterface;
use App\Http\Resources\User\UserResource;

class LoginController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function action(LoginRequest $request)
    {
        $request['user_id'] = Auth::id();

        $this->user->userLogin($request);

        $deviceData = [
            'user_id' => $request['user_id'],
            'platform' => $request['source'],
            'device_id' => $request['device_id'] ?? null
        ];

        $this->user->findOrNewDevice($deviceData);

        $this->user->activateUser();

        return (new UserResource($request->user()))
        ->message(trans('login.login_success'))
        ->additional(['meta' => $this->requestAccessToken($request['user_id'], $request['password'])]);
    }
}
