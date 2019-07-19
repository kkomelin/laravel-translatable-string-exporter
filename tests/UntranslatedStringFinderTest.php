<?php

namespace Tests;

class UntranslatedStringFinderTest extends BaseTestCase
{
    // @todo: Add more tests.

    public function testFinder()
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

        $language = 'es';
        $command = $this->artisan('translatable:inspect-translations', [
            'lang' => $language,
            '--export-first' => true
        ])
            ->expectsOutput(
                'Found ' . count($source) . ' untranslated ' .
                \Str::plural('string', count($source)) . ' in the ' .
                $language . '.json file:'
            );

        $expected = array_reverse($source);

        foreach ($expected as $str) {
            $command->expectsOutput($str);
        }

        $command->assertExitCode(0);
    }
}
