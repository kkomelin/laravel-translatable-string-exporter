<?php

namespace Tests;

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
}
