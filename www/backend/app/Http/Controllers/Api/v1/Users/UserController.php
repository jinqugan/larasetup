<?php
/**
 * Controller : UserController.
 *
 * This file used to handle user data
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */
 

namespace App\Http\Controllers\Api\v1\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\User\UserInterface;
use App\Traits\ResponseTrait;
use App\Traits\UserTrait;

class UserController extends Controller
{
    use ResponseTrait, UserTrait;

    protected $user;

    /**
     * Create a new controller instance.
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Regenerate new refresh token and access token
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function userRefreshToken(Request $request)
    {
        $result = $this->requestResponses();

        $oauthToken = $this->user->refreshAccessToken($request['refresh_token']);

        if (
            (isset($oauthToken->success) && !$oauthToken->success) ||
            (!empty($oauthToken->error))
        ) {
            $result = $this->requestErrors();

            $result['message'] = $oauthToken->message;
            $result['errors'] = [
                'refresh_token' => $oauthToken->message,
            ];
            $result['exception'] = $oauthToken->error;

            return response()->json($result, $this->unprocessableStatus);
        }

        $result['meta'] = $oauthToken;

        return response()->json($result, $this->successStatus);
    }

    /**
     * void user login session
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function userLogout(Request $request)
    {
        $result = $this->requestResponses();

        $user = Auth::user();

        $tokenId = $user->token()->id;

        $this->revokeAccessAndRefreshTokens($tokenId);

        return response()->json($result, $this->successStatus);
    }
}
