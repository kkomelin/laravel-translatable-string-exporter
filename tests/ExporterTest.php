<?php

namespace Tests;

class ExporterTest extends BaseTestCase
{
    /** @test */
    public function smoke_test()
    {
        $this->cleanLangsFolder();
        $this->createTestView("{{ __('name') }}");

        $this->artisan('translatable:export', ['lang' => 'bg,es'])
            ->expectsOutput('Translatable strings have been extracted and written to the bg.json file.')
            ->expectsOutput('Translatable strings have been extracted and written to the es.json file.')
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('lang/bg.json'));

        $bg = file_get_contents(resource_path('lang/bg.json'));
        $es = file_get_contents(resource_path('lang/es.json'));

        $this->assertEquals(['name' => 'name'], json_decode($bg, true));
        $this->assertEquals(['name' => 'name'], json_decode($es, true));
    }
}
