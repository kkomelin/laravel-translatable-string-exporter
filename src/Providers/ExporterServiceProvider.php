<?php

namespace KKomelin\TranslatableStringExporter\Providers;

use Illuminate\Support\ServiceProvider;
use KKomelin\TranslatableStringExporter\Console\ExportCommand;

class ExporterServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('translatable-string-exporter', function ($app) {
            return $app->make(\KKomelin\TranslatableStringExporter\Core\Exporter::class);
        });

        $this->app->singleton('command.translatable-string-exporter.export', function ($app) {
            return new ExportCommand($app['translatable-string-exporter']);
        });
        $this->commands('command.translatable-string-exporter.export');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'translatable-string-exporter',
            'command.translatable-string-exporter.export',
        );
    }
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/laravel-translatable-string-exporter.php' => config_path('laravel-translatable-string-exporter.php'),
        ]);
    }
}
