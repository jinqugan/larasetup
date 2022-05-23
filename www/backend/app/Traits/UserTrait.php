<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

trait UserTrait {
    /**
     * Generate phone international format (+60108888888).
     *
     * @return string
     */
    public function phoneFormatInternational($phoneNumber, $countryCode='')
    {
        $countryCode = $countryCode ?: config('constant.country_code');

        try {
            $intlNumber = PhoneNumber::make($phoneNumber, $countryCode)->formatE164();

            $intlNumber = str_replace('+', '', $intlNumber);
        } catch (Exception $e) {
            $intlNumber = '';
        }

        return $intlNumber;
    }

    /**
     * Generate international mobile dialing format.
     *
     * @return string
     */
    public function usernameBelongTo($username, $predefined=NULL)
    {
        $type = NULL;

        if ($predefined) {
            return $predefined;
        }

        if (is_numeric($username)) {
            $type = 'mobileno';
        } else {
            $type = 'email';
        }

        return $type;
    }

    /**
     * Generate token.
     *
     * @return bool
     */
    public function generateRandomToken()
    {
        $token = Str::random(60);

        return hash('sha256', $token);
    }

    /**
     * Revoke access token and refresh token by tokenId
     * @params string $tokenId
     *
     * @return bool
     */
    protected function revokeAccessAndRefreshTokens($tokenId)
    {
        $tokenRepository = app('Laravel\Passport\TokenRepository');
        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');

        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);

        return true;
    }
}
