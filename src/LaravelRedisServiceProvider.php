<?php

namespace Bilaliqbalr\LaravelRedis;

use Bilaliqbalr\LaravelRedis\Auth\UserProvider;
use Bilaliqbalr\LaravelRedis\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Bilaliqbalr\LaravelRedis\Commands\LaravelRedisCommand;

class LaravelRedisServiceProvider extends PackageServiceProvider
{
    private $name = 'laravel-redis';

    public function configurePackage(Package $package): void
    {
        $package
            ->name($this->name)
            ->hasConfigFile()
            ->hasCommand(LaravelRedisCommand::class);

        // Registering redis custom guard
        Auth::viaRequest($this->getConfig('guard'), function (Request $request) {
            return app(User::class)->getUserByAuthToken();
        });

        // Registering redis custom user provider
        Auth::provider($this->getConfig('provider'), function ($app, array $config) {
            return new UserProvider();
        });
    }

    public function getConfig($key)
    {
        return config("{$this->name}.{$key}");
    }
}
