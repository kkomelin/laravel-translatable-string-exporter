<?php

namespace KKomelin\TranslatableStringExporter\Providers;

use Illuminate\Support\ServiceProvider;
use KKomelin\TranslatableStringExporter\Console\InspectTranslationsCommand;
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

        // Export translatable strings command.

        $this->app->singleton('translatable-string-exporter-exporter', function ($app) {
            return $app->make('\KKomelin\TranslatableStringExporter\Core\Exporter');
        });

        $this->app->singleton('command.translatable-string-exporter-exporter.export', function ($app) {
            return new ExportCommand($app['translatable-string-exporter-exporter']);
        });
        $this->commands('command.translatable-string-exporter-exporter.export');

        // Inspect translations command.

        $this->app->singleton('translatable-string-exporter-inspect-translations', function ($app) {
            return $app->make('\KKomelin\TranslatableStringExporter\Core\UntranslatedStringFinder');
        });

        $this->app->singleton('command.translatable-string-exporter-inspect-translations.inspect-translations', function ($app) {
            return new InspectTranslationsCommand(
                $app['translatable-string-exporter-exporter'],
                $app['translatable-string-exporter-inspect-translations']
            );
        });
        $this->commands('command.translatable-string-exporter-inspect-translations.inspect-translations');
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
            'translatable-string-exporter-inspect-translations',
            'command.translatable-string-exporter-inspect-translations.inspect-translations',
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
