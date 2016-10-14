<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Session;

class SessionUserProvider implements UserProvider
{
    /**
     * The hasher implementation.
     *
     * @var HasherContract
     */
    protected $hasher;

    /**
     * The provider config.
     *
     * @var array
     */
    protected $config;

    public function __construct(HasherContract $hasher, $config)
    {
        $this->hasher = $hasher;
        $this->config = $config;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = Session::get('current_user');
        if (!$user) {
            return null;
        }

        if ($user->getAuthIdentifier() == $identifier) {
            return $user;
        }

        return null;
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed $identifier
     * @param  string $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $user = Session::get('current_user');
        if (!$user) {
            return null;
        }

        if ($user->getAuthIdentifier() == $identifier && $user->getRememberToken() == $token) {
            return $user;
        }

        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user = Session::get('current_user');
        if (!$user) {
            return;
        }

        $user->setRememberToken($token);
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $user = Session::get('current_user');
        if (!$user) {
            return null;
        }

        foreach ($credentials as $key => $value) {
            if ($key == $user->getAuthIdentifierName() && $value == $user->getAuthIdentifier()) {
                return $user;
            }
            if ($key == $user->getRememberTokenName() && $value == $user->getRememberToken()) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}