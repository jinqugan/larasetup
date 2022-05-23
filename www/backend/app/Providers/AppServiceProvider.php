<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        /**
         * Remove default wrapping attribute when json return toArray()
         */
        JsonResource::wrap(JSON_WRAP);

        /*************************************************
         ** All validator handle for global custom rule **
         *************************************************/
        Validator::extend('alphabert', function ($attribute, $value, $parameters, $validator) {
            if (!ctype_alpha($value)) {
                return false;
            }

            return true;
        }, trans('general.only_alphabert_allowed'));

        /**
         * All validator handle for global custom rule
         */
        Validator::extend('specialcharacter', function ($attribute, $value, $parameters, $validator) {
            if(preg_match("/[".SPECIALCHAR."]/",$value)){
                return false;
            }

            return true;
        }, trans('general.specialchar_not_allowed'));
    }
}
