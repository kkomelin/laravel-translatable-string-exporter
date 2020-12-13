<?php

namespace Tests;

use KKomelin\TranslatableStringExporter\Core\Exporter;

class ExporterTest extends BaseTestCase
{
    public function testTranslationFilesCreation()
    {
        $this->cleanLangsFolder();

        $this->createTestView("{{ __('name') }}");

        $this->artisan('translatable:export', ['lang' => 'bg,es'])
            ->expectsOutput('Translatable strings have been extracted and written to the bg.json file.')
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $this->assertFileExists($this->getTranslationFilePath('bg'));
        $this->assertFileExists($this->getTranslationFilePath('es'));

        $bg_content = $this->getTranslationFileContent('bg');
        $es_content = $this->getTranslationFileContent('es');

        $this->assertEquals(['name' => 'name'], $bg_content);
        $this->assertEquals(['name' => 'name'], $es_content);
    }

    public function testTranslationSorting()
    {

        $this->cleanLangsFolder();

        $source = [
            'name3',
            'name2',
            'name1',
        ];

        $template_strings = array_map(function ($translatable_string) {
            return "{{ __('" . $translatable_string . "') }}";
        }, $source);

        $this->createTestView(implode(' ', $template_strings));

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $expected = [
            'name1' => 'name1',
            'name2' => 'name2',
            'name3' => 'name3',
        ];

        $actual = $this->getTranslationFileContent('es');

        $this->assertEquals($expected, $actual);
    }

    public function testTranslationFunctionNames()
    {

        $this->cleanLangsFolder();

        $view = "{{ __('name__') }} " .
            "@lang('name_lang') " .
            "{{ _t('name_t') }} " .
            "{{ __('name__space_end' ) }} " .
            "@lang( 'name_lang_space_start') " .
            "{{ _t( 'name_t_space_both' ) }} " .
            "{{ _t(  'name_t_double_space'  ) }}";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = [
            'name__' => 'name__',
            'name_lang' => 'name_lang',
            'name_t' => 'name_t',
            'name__space_end' => 'name__space_end',
            'name_lang_space_start' => 'name_lang_space_start',
            'name_t_space_both' => 'name_t_space_both',
            'name_t_double_space' => 'name_t_double_space',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testMultiLineSupportDisabled()
    {
        $this->cleanLangsFolder();

        $view = "{{ __('single line') }} " .
            "{{ __('translation.keys') }} " .
            // Verify legacy behaviours:
            // 1) Strings including escaped newlines (\n) are processed
            "{{ __('escaped\\nnewline') }}" .
            // 2) Strings including un-escaped newlines are ignored.
            "{{ __(\"ignored\nmultiple\nline\nstring\") }}";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'single line' => 'single line',
            'translation.keys' => 'translation.keys',
            'escaped\\nnewline' => 'escaped\\nnewline',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testMultiLineSupportEnabled()
    {
        $this->app['config']->set('laravel-translatable-string-exporter.allow-newlines', true);

        $this->cleanLangsFolder();

        $view = "{{ __('single line') }} " .
                "{{ __('translation.keys') }} " .
            // No change to 1st legacy behaviour:
            // 1) Strings including escaped newlines (\n) are processed
            "{{ __('escaped\\nnewline') }}" .
            // Un-escaped newlines are now also processed.
            // 2) Strings including un-escaped newlines are ignored.
            "{{ __(\"detected\nmultiple\nline\nstring\") }}";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'single line' => 'single line',
            'translation.keys' => 'translation.keys',
            'escaped\nnewline' => 'escaped\nnewline',
            "detected\nmultiple\nline\nstring" => "detected\nmultiple\nline\nstring",
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdatingTranslations()
    {
        $this->cleanLangsFolder();

        // Create a translation file ourselves.

        $existing_translations = ['name1_en' => 'name2_es'];

        $content = json_encode($existing_translations);

        $this->writeToTranslationFile('es', $content);

        // 1. Now create a test view with the same translatable string.

        $this->createTestView("{{ __('name1_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        // Since the translatable string from view matches the existing translation, we don't override it.

        $expected = $existing_translations;

        $this->assertEquals($expected, $actual);

        // 2. Now let's add a new translation to the view.

        $this->createTestView("{{ __('name1_en') . __('name2_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        // Since the translatable string from view is not yet translated, then we simply add it to the translation file.

        $expected = $existing_translations + ['name2_en' => 'name2_en'];

        $this->assertEquals($expected, $actual);

        // 3. Next let's remove the first translatable string from the view.

        $this->createTestView("{{ __('name2_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        // All translations which are not found in views should be deleted from translation files.

        $expected = ['name2_en' => 'name2_en'];

        $this->assertEquals($expected, $actual);
    }

    public function testPersistentTranslations()
    {
        $this->cleanLangsFolder();

        // 1. Create a translation file ourselves.

        $existing_translations = [
            'name1_en' => 'name1_es',
            'name2_en' => 'name2_es',
            'name3_en' => 'name3_es',
        ];

        $content = json_encode($existing_translations);

        $this->writeToTranslationFile('es', $content);

        // 2. Create a file with the keys of any strings which should persist even if they are not contained in the views.

        $persistentContent = json_encode(['name2_en']);
        $this->writeToTranslationFile(Exporter::PERSISTENT_STRINGS_FILENAME_WO_EXT, $persistentContent);

        // 3. Create a test view only containing one of the non-persistent strings, and a new string.

        $this->createTestView("{{ Existing string: __('name1_en') New string: __('name4_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        // The missing, non-persistent, strings should be removed. The rest should remain.

        $expected = [
            'name1_en' => 'name1_es',
            'name2_en' => 'name2_es',
            'name4_en' => 'name4_en',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testAddingPersistentStringsToExport()
    {
        $this->app['config']->set(
            'laravel-translatable-string-exporter.add-persistent-strings-to-translations',
            true
        );

        $this->cleanLangsFolder();

        // 1. Create a translation file ourselves.

        $existing_translations = [
            'name1_en' => 'name1_es',
            'name2_en' => 'name2_es',
            'name3_en' => 'name3_es',
        ];

        $content = json_encode($existing_translations);

        $this->writeToTranslationFile('es', $content);

        // 2. Create a file with the keys of any strings which should persist
        // even if they are not contained in the views.

        $persistentContent = json_encode(['name3_en', 'name5_en']);
        $this->writeToTranslationFile(Exporter::PERSISTENT_STRINGS_FILENAME_WO_EXT, $persistentContent);

        // 3. Create a test view only containing a new string and a string that is also in persistent strings.

        $this->createTestView("{{ __('name1_en') . __('name2_en') . __('name3_en') . __('name4_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        // The new and persistent strings should be added. The rest should remain.

        $expected = array_merge($existing_translations, [
            'name4_en' => 'name4_en',
            'name5_en' => 'name5_en',
        ]);

        $this->assertEquals($expected, $actual);
    }
}
