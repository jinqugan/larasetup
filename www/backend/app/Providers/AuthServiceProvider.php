<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->userPassport();
    }

    private function userPassport()
    {
        $header = app('request')->header();
        $source = !empty($header[strtolower(HEADER_SOURCE)]) ? $header[strtolower(HEADER_SOURCE)][0] : NULL;
        $tokenExpiresIn = config('constant.source_token_expiry_in');
        $refreshTokenExpiresIn = config('constant.source_refresh_token_expiry_in');
        $tokensExpireIn = now()->addMinutes(30);
        $refreshTokensExpireIn = now()->addDays(1);

        if (!empty($tokenExpiresIn[$source])) {
            $tokensExpireIn = date(DATE_TIME, strtotime($tokenExpiresIn[$source]));
        }

        if (!empty($refreshTokenExpiresIn[$source])) {
            $refreshTokensExpireIn = date(DATE_TIME, strtotime($refreshTokenExpiresIn[$source]));
        }

        Passport::routes();
        // Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::tokensExpireIn(new Carbon($tokensExpireIn));
        Passport::refreshTokensExpireIn(new Carbon($refreshTokensExpireIn));
        Passport::personalAccessTokensExpireIn(new Carbon($tokensExpireIn));
     
    }
}
