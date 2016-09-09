<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Connectors\ConnectionFactory;

class MysqlUserProvider implements UserProvider
{
    /**
     * The database connection factory.
     *
     * @var ConnectionFactory
     */
    protected $connectionFactory;

    /**
     * The database config.
     *
     * @var array
     */
    protected $databaseConfig;

    /**
     * The provider config.
     *
     * @var array
     */
    protected $config;

    public function __construct(ConnectionFactory $connectionFactory, $databaseConfig, $config)
    {
        $this->connectionFactory = $connectionFactory;
        $this->databaseConfig    = $databaseConfig;
        $this->config            = $config;
        dd($this->databaseConfig);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // TODO: Implement retrieveById() method.
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
        // TODO: Implement retrieveByToken() method.
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
        // TODO: Implement updateRememberToken() method.
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
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

        $conn = $this->connectionFactory->make([
            'host' => 'localhost',
            'port' => 3306,
            'username' => $user->getAuthIdentifierName()
        ]);
    }
}