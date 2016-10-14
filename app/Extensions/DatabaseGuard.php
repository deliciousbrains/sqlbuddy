<?php

namespace App\Extensions;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DatabaseGuard implements StatefulGuard
{
    /**
     * @var UserProvider
     */
    protected $provider;

    /**
     * @var ConnectionFactory
     */
    protected $connectionFactory;

    /**
     * @var array
     */
    protected $dbConfig;

    public function __construct(UserProvider $provider, ConnectionFactory $connectionFactory, $dbConfig)
    {
        $this->provider          = $provider;
        $this->connectionFactory = $connectionFactory;
        $this->dbConfig          = $dbConfig;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return Session::has('current_user');
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return Session::get('current_user');
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        $user = $this->user();

        if (!$user) {
            return null;
        }

        return $user->getAuthIdentifier();
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        Session::put('current_user', $user);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array $credentials
     * @param  bool $remember
     * @param  bool $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        $user = new User($credentials);

        if ($this->provider->validateCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        return false;
    }

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param  array $credentials
     * @return bool
     */
    public function once(array $credentials = [])
    {
        return false;
    }

    /**
     * Log a user into the application.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  bool $remember
     * @return void
     */
    public function login(Authenticatable $user, $remember = false)
    {
        /*$this->connectionFactory->make([

        ]);*/

        if ($remember) {
            if (empty($user->getRememberToken())) {
                $this->provider->updateRememberToken($user, Str::random(60));
            }
        }

        $this->setUser($user);
    }

    /**
     * Log the given user ID into the application.
     *
     * @param  mixed $id
     * @param  bool $remember
     * @return \Illuminate\Contracts\Auth\Authenticatable|bool
     */
    public function loginUsingId($id, $remember = false)
    {
        return false;
    }

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param  mixed $id
     * @return bool
     */
    public function onceUsingId($id)
    {
        return false;
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember()
    {
        return false;
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        Session::forget('current_user');
    }
}