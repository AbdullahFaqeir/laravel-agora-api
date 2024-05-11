<?php

declare(strict_types = 1);

namespace AbdullahFaqeir\LaravelAgoraApi;

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;


class LaravelAgoraApiServiceProvider extends PackageServiceProvider
{
    public function boot(): void
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/channels.php');

        $this->publishes([
            __DIR__.'/../config/laravel-agora-api.php.php' => config_path('laravel-agora-api.php'),
        ], 'laravel-agora-api-config');

        $this->publishes([
            __DIR__.'/../resources/js' => resource_path('js/vendor/laravel-agora-api'),
        ], 'laravel-agora-api-js');

        $this->publishes([
            __DIR__.'/../resources/css/agora-component-styles.css' => resource_path('css/vendor/agora-component-styles.css'),
        ], 'laravel-agora-api-css');
    }

    protected function routeConfiguration(): array
    {
        return [
            'prefix'     => config('laravel-agora-api.routes.prefix'),
            'middleware' => config('laravel-agora-api.routes.middleware'),
        ];
    }

    public function configurePackage(Package $package): void
    {
        $package->name('laravel-agora-api')
                ->hasConfigFile('laravel-agora-api')
                ->hasAssets()
                ->publishesServiceProvider('LaravelAgoraApi')
                ->hasRoute('web')
                ->hasRoutes('channels')
                ->hasInstallCommand(function (InstallCommand $command) {
                    $command->publishConfigFile()
                            ->publishAssets()
                            ->copyAndRegisterServiceProviderInApp()
                            ->askToStarRepoOnGitHub('abdullahfaqeir/laravel-agora');
                });
    }
}
