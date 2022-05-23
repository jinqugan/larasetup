<?php
/**
 * ServiceProvider : UserServiceProvider.
 *
 * This file used to register UserRepositoryService
 *
 * @author JQ Gan <jinqgan@gmail.com>
 *
 * @version 1.0
 */

namespace App\Repositories\User;

use App\Models\User;
use App\Models\OneTimePassword;
use App\Models\Status;
use App\Models\Device;
use App\Models\Address;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Registers the UserInterface with Laravels IoC Container.
     */
    public function register()
    {
        // Bind the returned class to the namespace 'App\Repositories\User\UserInterface
        $this->app->bind('App\Repositories\User\UserInterface', function ($app) {
            return new UserRepository(
                new User(), new OneTimePassword(), new Status(), new Device(), new Address()
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
