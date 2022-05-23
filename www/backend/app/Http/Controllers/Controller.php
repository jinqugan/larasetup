<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function requestAccessToken($userId, $password=null, $scopes=null)
    {
        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();

        if (!$client) {
            return 'oauth_clients not found';
        }

        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $userId ?? auth()->id(),
            'password' => $password ?? 'bypass',
        ];

        $request = Request::create('/oauth/token', 'POST', $data);

        $oauthToken = json_decode(app()->handle($request)->getContent());

        return $oauthToken;
    }

    /**
     * Generate token.
     *
     * @return bool
     */
    public function generateToken()
    {
        $token = Str::random(60);

        return hash('sha256', $token);
    }

    /**
     * Generate 6 random digit number for tac.
     *
     * @return int
     */
    protected function generateOtp()
    {
        return rand(100000, 999999);
    }
}
