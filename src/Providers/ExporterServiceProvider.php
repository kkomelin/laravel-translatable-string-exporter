<?php

namespace KKomelin\TranslatableStringExporter\Providers;

use Illuminate\Support\ServiceProvider;
use KKomelin\TranslatableStringExporter\Console\DisplayUntranslatedCommand;
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
        // @todo: Optimize the following code. Switch to ::class after 2.0.0 version.

        // Export.

        $this->app->singleton('translatable-string-exporter-exporter', function ($app) {
            return $app->make('\KKomelin\TranslatableStringExporter\Core\Exporter');
        });

        $this->app->singleton('command.translatable-string-exporter-exporter.export', function ($app) {
            return new ExportCommand($app['translatable-string-exporter-exporter']);
        });
        $this->commands('command.translatable-string-exporter-exporter.export');

        // DisplayUntranslated.

        $this->app->singleton('translatable-string-exporter-display-untranslated', function ($app) {
            return $app->make('\KKomelin\TranslatableStringExporter\Core\UntranslatedStringFinder');
        });

        $this->app->singleton('command.translatable-string-exporter-display-untranslated.display-untranslated', function ($app) {
            return new DisplayUntranslatedCommand(
                $app['translatable-string-exporter-exporter'],
                $app['translatable-string-exporter-display-untranslated']
            );
        });
        $this->commands('command.translatable-string-exporter-display-untranslated.display-untranslated');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'translatable-string-exporter-exporter',
            'command.translatable-string-exporter-exporter.export',
            'translatable-string-exporter-display-untranslated',
            'command.translatable-string-exporter-display-untranslated.display-untranslated',
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
            __DIR__.'/../../config/laravel-translatable-string-exporter.php' => config_path('laravel-translatable-string-exporter.php'),
        ]);
    }
}
