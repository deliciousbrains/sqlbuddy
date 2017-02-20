<?php

namespace App\Providers;

use App\Extensions\DatabaseGuard;
use App\Extensions\SessionUserProvider;
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
            // Return an instance of Illuminate\Contracts\Auth\UserProvider...
        });
    }
}
