<?php

namespace KKomelin\TranslatableStringExporter\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use KKomelin\TranslatableStringExporter\Core\Exporter;
use KKomelin\TranslatableStringExporter\Core\UntranslatedStringFinder;
use Symfony\Component\Console\Input\InputArgument;

class InspectTranslationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translatable:inspect-translations {lang} {--export-first}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan a language file for unstranslated string and display them in the console.';

    /**
     * ExtractCommand constructor.
     *
     * @param  \KKomelin\TranslatableStringExporter\Core\Exporter  $exporter
     * @param  \KKomelin\TranslatableStringExporter\Core\UntranslatedStringFinder  $finder
     * @return void
     */
    public function __construct(protected Exporter $exporter, protected UntranslatedStringFinder $finder)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $language = $this->argument('lang');

        $export_first = $this->option('export-first');

        if ($export_first) {
            $this->exporter->export($language);
            $this->info('Translatable strings have been extracted and written to the ' . $language . '.json file.');
        }

        // Find untranslated strings in the given language file.
        $untranslated_strings = $this->finder->find($language);

        if ($untranslated_strings === false) {
            $this->info('Did not find ' . $language . '.json file. Use --export-first option.');

            return static::FAILURE;
        }

        if (empty($untranslated_strings)) {
            $this->info('Did not find any untranslated strings in the ' . $language . '.json file.');

            return static::FAILURE;
        }

        $count_untranslated = count($untranslated_strings);

        // Display untranslated strings.
        $this->info(
            'Found ' . $count_untranslated . ' untranslated ' .
                Str::plural('string', $count_untranslated) . ' in the ' .
                $language . '.json file:'
        );

        foreach ($untranslated_strings as $untranslated_string) {
            $this->info($untranslated_string);
        }

        return static::SUCCESS;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'lang',
                InputArgument::REQUIRED,
                'A language code for which untranslated strings are detected, e.g. "es".',
            ],
        ];
    }
}
