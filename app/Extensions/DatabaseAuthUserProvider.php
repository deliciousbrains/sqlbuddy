<?php

namespace App\Extensions;

use App\User;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\DatabaseManager;

class DatabaseAuthUserProvider implements UserProvider
{
    /**
     * @var DatabaseManager
     */
    protected $dbManager;

    public function __construct(DatabaseManager $dbManager)
    {
        $this->dbManager = $dbManager;
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        if ($identifier) {
            return new User(['user' => $identifier]);
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
        return $this->retrieveById($identifier);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  string $token
     * @return void
     */
    public function updateRememberToken(UserContract $user, $token)
    {
        //
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->retrieveById($credentials['email']);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $connections        = config('database.connections');
        $selectedConnection = $this->dbManager->getDefaultConnection();

        if (!isset($connections[$selectedConnection])) {
            return false;
        }

        $conn = $connections[$selectedConnection];

        $dsn = $conn['driver'];
        if (isset($conn['host'])) {
            $dsn .= ':host=' . $conn['host'];
        }
        if (isset($conn['port'])) {
            $dsn .= ':' . $conn['port'];
        }

        try {
            $pdo = new PDOConnection($dsn, $credentials['email'], $credentials['password']);

            // Update the connections config to user our validated credentials
            $this->updateConnectionsConfig($connections, $credentials['email'], $credentials['password']);

            return true;
        } catch (PDOException $e) {
            // Invalid credentials
        }

        return false;
    }

    /**
     * @param array $connections
     * @param string $username
     * @param string $password
     */
    protected function updateConnectionsConfig($connections, $username, $password)
    {
        foreach ($connections as $connKey => $connVal) {
            if (isset($connVal['username'])) {
                $connections[$connKey]['username'] = $username;
            }
            if (isset($connVal['password'])) {
                $connections[$connKey]['password'] = $password;
            }
        }

        config(['database.connections' => $connections]);
    }
}
