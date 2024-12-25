<?php

namespace Chinmay\LaravelCommandDocumentor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            DocumentCommand::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/documentor.php' => config_path('documentor.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/documentor.php', 'documentor'
        );
    }
}