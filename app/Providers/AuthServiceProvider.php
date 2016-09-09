<?php

namespace App\Providers;

use App\Extensions\MysqlUserProvider;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        \Auth::provider('mysql', function ($app, array $config) {
            $app->configure('database');

            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
            return new MysqlUserProvider($app['db.factory'], $app['config']['database'], $config);
        });
    }
}
