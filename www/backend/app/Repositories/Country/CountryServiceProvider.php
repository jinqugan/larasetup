<?php
/**
 * ServiceProvider : CountryServiceProvider.
 *
 * This file used to register CountryRepositoryService
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\Country;

use App\Models\Country;
use App\Models\State;
use App\Models\City;

// use Spatie\Permission\Models\Role;
use Illuminate\Support\ServiceProvider;

class CountryServiceProvider extends ServiceProvider
{
    /**
     * Registers the CountryInterface with Laravels IoC Container.
     */
    public function register()
    {
        // Bind the returned class to the namespace 'App\Repositories\Country\CountryInterface
        $this->app->bind('App\Repositories\Country\CountryInterface', function ($app) {
            return new CountryRepository(
                new Country(), new State(), new City()
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
