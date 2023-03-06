<?php

namespace Tests;

use KKomelin\TranslatableStringExporter\Core\Exporter;
use Tests\__fixtures\classes\Transformer;

class ExporterTest extends BaseTestCase
{
    public function testTranslationFilesCreation()
    {
        $this->removeJsonLanguageFiles();

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
        $this->removeJsonLanguageFiles();

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
        $this->removeJsonLanguageFiles();

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

    public function testQuotationMarkEscapingPR52()
    {
        $this->removeJsonLanguageFiles();

        $view = <<<EOD
{{ __("He said \"WOW\".") }}
{{ __('We\'re amazing!') }}
@lang("You're pretty great!")
@lang("You\"re pretty great!")
{{ __("Therefore, we automatically look for columns named something like \"Last name\", \"First name\", \"E-mail\" etc.") }}
EOD;

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = [
            'He said \"WOW\".' => 'He said \"WOW\".',
            'We\'re amazing!' => 'We\'re amazing!',
            "You're pretty great!" => "You're pretty great!",
            'You\"re pretty great!' => 'You\"re pretty great!',
            'Therefore, we automatically look for columns named something like \"Last name\", \"First name\", \"E-mail\" etc.' =>
                'Therefore, we automatically look for columns named something like \"Last name\", \"First name\", \"E-mail\" etc.',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testMultiLineSupportDisabled()
    {
        $this->removeJsonLanguageFiles();

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

        $this->removeJsonLanguageFiles();

        $view = "{{ __('single line') }} " .
                "{{ __('translation.keys') }} " .
            // No change to 1st legacy behaviour:
            // 1) Strings including escaped newlines (\n) are processed
            "{{ __('escaped\\nnewline') }}" .
            // Un-escaped newlines are now also processed.
            // 2) Strings including un-escaped newlines are ignored.
            "{{ __(\"detected\nmultiple\nline\nstring\") }}" .
            // test whether strings which have new line between function and string are also detected
            "{{ __(\n\"string between new line\"\n) }}";

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
            "string between new line" => "string between new line",
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testNewLineParametersIssue57()
    {
        $this->removeJsonLanguageFiles();

        $view = <<<EOD
            pushGenericFeedback(__(
                "This is some generic key with a :var1 and :var2 in it 1",
                ["var1" => "variable", "var2" => "another variable"]
            ));

            pushGenericFeedback(
                __("This is some generic key with a :var1 and :var2 in it 2",
                ["var1" => "variable", "var2" => "another variable"]
            ));
        EOD;

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'This is some generic key with a :var1 and :var2 in it 1' =>
                'This is some generic key with a :var1 and :var2 in it 1',
            'This is some generic key with a :var1 and :var2 in it 2' =>
                'This is some generic key with a :var1 and :var2 in it 2',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testNewLineParametersIssue45()
    {
        $this->removeJsonLanguageFiles();

        $view = <<<EOD
            sprintf(__('A required parameter ("%s") was not found.'), ["variable"]);
        EOD;

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'A required parameter ("%s") was not found.' =>
                'A required parameter ("%s") was not found.',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testUpdatingTranslations()
    {
        $this->removeJsonLanguageFiles();

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
        $this->removeJsonLanguageFiles();

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

        $this->removeJsonLanguageFiles();

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

    public function testIgnoreTranslationKeysEnabled()
    {
        $this->app['config']->set('laravel-translatable-string-exporter.exclude-translation-keys', true);

        $this->removeJsonLanguageFiles();

        $view = "{{ __('text to translate') }} " .
            "{{ __('string with a dot.') }} " .
            "{{ __('string with a dot. in the middle') }} " .
            "{{ __('menu.unknown') }} " .
            "{{ __('menu.submenu1') }} " .
            "{{ __('menu.submenu1.item1') }} " .
            "{{ __('menu.item1') }} ";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'text to translate' => 'text to translate',
            'string with a dot.' => 'string with a dot.',
            'string with a dot. in the middle' => 'string with a dot. in the middle',
            'menu.unknown' => 'menu.unknown',
            'menu.submenu1' => 'menu.submenu1',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testPuttingUntranslatedStringsToTop()
    {
        $this->app['config']->set(
            'laravel-translatable-string-exporter.put-untranslated-strings-at-the-top',
            true
        );

        $this->removeJsonLanguageFiles();

        // 1. Create a translation file with all srings translated.

        $existing_translations = [
            'name1_en' => 'name1_es',
            'name2_en' => 'name2_es',
            'name3_en' => 'name3_es',
        ];

        $content = json_encode($existing_translations);

        $this->writeToTranslationFile('es', $content);

        // 2. [Sorting disabled] Create a test view with translated and untranslated strings.

        $this->app['config']->set(
            'laravel-translatable-string-exporter.sort-keys',
            false
        );

        $this->createTestView("{{ __('name1_en') . __('name2_en') . __('name3_en') . __('name5_en') . __('name4_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = array_merge([
            'name5_en' => 'name5_en',
            'name4_en' => 'name4_en',
        ], $existing_translations);

        // Check that arrays are equivalent taking into account element order.
        $this->assertTrue($expected === $actual, 'Expected and actual arrays are not equivalent.');

        // 3. [Sorting enabled] Create a test view with translated and untranslated strings.

        $this->app['config']->set(
            'laravel-translatable-string-exporter.sort-keys',
            true
        );

        $this->createTestView("{{ __('name1_en') . __('name2_en') . __('name3_en') . __('name5_en') . __('name4_en') }}");

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');

        $expected = array_merge([
            'name4_en' => 'name4_en',
            'name5_en' => 'name5_en',
        ], $existing_translations);

        // Check that arrays are equivalent taking into account element order.
        $this->assertTrue($expected === $actual, 'Expected and actual arrays are not equivalent.');
    }

    public function testSettingAFunctionToTransform()
    {
        $this->app['config']->set('laravel-translatable-string-exporter.functions.aFunction', fn ($s) => \strtoupper(\str_replace(["-","_"], " ", $s)));

        $this->removeJsonLanguageFiles();

        $view = "{{ aFunction('text-to-translate') }}";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'TEXT TO TRANSLATE' => 'TEXT TO TRANSLATE',
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testSettingACallableToTransform()
    {
        $this->app['config']->set('laravel-translatable-string-exporter.functions.staticMethod', [Transformer::class, 'staticMethod']);
        $this->app['config']->set('laravel-translatable-string-exporter.functions.publicMethod', [new Transformer(), 'publicMethod']);

        $this->removeJsonLanguageFiles();

        $view = "{{ staticMethod('static-text-to-translate') }} {{ publicMethod('public-text-to-translate') }}";

        $this->createTestView($view);

        $this->artisan('translatable:export', ['lang' => 'es'])
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $actual = $this->getTranslationFileContent('es');
        $expected = [
            'STATIC TEXT TO TRANSLATE' => 'STATIC TEXT TO TRANSLATE',
            'PUBLIC TEXT TO TRANSLATE' => 'PUBLIC TEXT TO TRANSLATE',
        ];

        $this->assertEquals($expected, $actual);
    }
}
