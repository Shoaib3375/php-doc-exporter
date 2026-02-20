<?php

namespace Shoaib3375\PhpDocExporter;

use Illuminate\Support\ServiceProvider;

class PhpDocExporterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Config::class, function ($app) {
            return new Config(
                config('php-doc-exporter.main_token'),
                config('php-doc-exporter.safe_token')
            );
        });

        $this->app->singleton(DocumentExporter::class, function ($app) {
            return new DocumentExporter($app->make(Config::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/php-doc-exporter.php' => config_path('php-doc-exporter.php'),
            ], 'config');
        }
    }
}
