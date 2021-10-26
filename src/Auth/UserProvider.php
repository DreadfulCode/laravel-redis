<?php

namespace Bilaliqbalr\LaravelRedis\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as AuthUserProvider;
use Illuminate\Support\Facades\Hash;


class UserProvider implements AuthUserProvider
{
    private $user;

    public function __construct($app, $config)
    {
        $this->user = app($config['model']);
    }

    public function retrieveById($identifier)
    {
        $user = $this->user->get($identifier);

        return $this->getGenericUser($user);
    }

    public function retrieveByToken($identifier, $token)
    {
        $user = $this->user::searchByApiToken($token);

        return $this->getGenericUser($user);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->update([
            $user->getRememberTokenName(), $token
        ]);
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (! array_key_exists('email', $credentials)) {
            return null;
        }
        if (empty($credentials) ||
            (count($credentials) === 1 &&
             array_key_exists('password', $credentials))) {
            return null;
        }

        // User is a class from Laravel Auth System
        $user = $this->user->login($credentials['email'], $credentials['password']);

        return $this->getGenericUser($user);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (! array_key_exists('password', $credentials)) {
            return false;
        }

        return Hash::check(
            $credentials['password'], $user->getAuthPassword()
        );
    }

    protected function getGenericUser($user)
    {
        return $user;
    }
}
