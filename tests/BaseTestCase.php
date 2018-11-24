<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use KKomelin\TranslatableStringExporter\Providers\ExporterServiceProvider;

class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ExporterServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . '__fixtures');
        $app['config']->set('laravel-translatable-string-exporter.directories', [
            'resources',
        ]);

        $app['config']->set('laravel-translatable-string-exporter.sort-keys', true);

        $app['config']->set('laravel-translatable-string-exporter.functions', [
            '__',
            '_t',
            '@lang',
        ]);
    }

    protected function cleanLangsFolder()
    {
        $files = glob(base_path('resources/lang/*.json')); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    protected function createTestView($content)
    {
        file_put_contents(resource_path('views/index.blade.php'), $content);
    }

    protected function getTranslationFilePath($language)
    {
        return resource_path('lang/' . $language . '.json');
    }

    protected function getTranslationFileContent($language)
    {
        $path = $this->getTranslationFilePath($language);
        $content = file_get_contents($path);
        return json_decode($content, true);
    }

    protected function writeToTranslationFile($language, $content)
    {
        $path = $this->getTranslationFilePath($language);
        file_put_contents($path, $content);
    }
}
