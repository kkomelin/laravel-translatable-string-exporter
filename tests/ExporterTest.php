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

        $this->createTestView("{{ __('name__') }} @lang('name_lang') {{ _t('name_t') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = [
            'name__' => 'name__',
            'name_lang' => 'name_lang',
            'name_t' => 'name_t',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testTranslationNodeNames()
    {

        $this->cleanLangsFolder();

        $this->createTestView("text<lang>name_lang</lang>text<custom>name_custom</custom>text");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = [
            'name_lang' => 'name_lang',
            'name_custom' => 'name_custom',
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
}
