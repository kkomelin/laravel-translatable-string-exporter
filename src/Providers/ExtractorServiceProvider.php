<?php

namespace KKomelin\TranslatableStringExtractor\Providers;

use Illuminate\Support\ServiceProvider;
use KKomelin\TranslatableStringExtractor\Console\ExtractCommand;

class ExtractorServiceProvider extends ServiceProvider {

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
        $this->app->singleton('translatable-string-extractor', function ($app) {
            return $app->make(\KKomelin\TranslatableStringExtractor\Extractor::class);
        });

        $this->app->singleton('command.translatable-string-extractor.extract', function ($app) {
            return new ExtractCommand($app['translatable-string-extractor']);
        });
        $this->commands('command.translatable-string-extractor.extract');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array(
            'translatable-string-extractor',
            'command.translatable-string-extractor.extract',
        );
    }
}
