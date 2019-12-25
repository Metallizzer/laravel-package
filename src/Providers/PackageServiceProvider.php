<?php

namespace Metallizzer\Package\Providers;

use Illuminate\Support\ServiceProvider;
use Metallizzer\Package\Console\Commands;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/package.php', 'package'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\ClassMakeCommand::class,
                Commands\ConsoleMakeCommand::class,
                Commands\ControllerMakeCommand::class,
                Commands\EventMakeCommand::class,
                Commands\ExceptionMakeCommand::class,
                Commands\FactoryMakeCommand::class,
                Commands\JobMakeCommand::class,
                Commands\MailMakeCommand::class,
                Commands\MiddlewareMakeCommand::class,
                Commands\MigrateMakeCommand::class,
                Commands\ModelMakeCommand::class,
                Commands\NotificationMakeCommand::class,
                Commands\PackageMakeCommand::class,
                Commands\ProviderMakeCommand::class,
                Commands\RequestMakeCommand::class,
                Commands\ResourceMakeCommand::class,
                Commands\RuleMakeCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/package.php' => config_path('package.php'),
            ], 'config');
        }
    }
}
