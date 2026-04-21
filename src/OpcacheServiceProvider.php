<?php

namespace Appstract\Opcache;

use Illuminate\Support\ServiceProvider;

class OpcacheServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Clear::class,
                Commands\Config::class,
                Commands\Status::class,
                Commands\Compile::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/opcache.php' => config_path('opcache.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/opcache.php', 'opcache');

        if (! config('opcache.enabled', true)) {
            return;
        }

        $this->app->router->group([
            'middleware' => [Http\Middleware\Request::class],
            'prefix'     => config('opcache.prefix'),
        ], function ($router) {
            require __DIR__.'/Http/routes.php';
        });
    }
}
