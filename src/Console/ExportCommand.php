<?php

namespace KKomelin\TranslatableStringExporter\Console;

use Illuminate\Console\Command;
use KKomelin\TranslatableStringExporter\Core\Exporter;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translatable:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translatable strings for a language to a JSON file.';

    /**
     * ExportCommand constructor.
     *
     * @param  \KKomelin\TranslatableStringExporter\Core\Exporter  $exporter
     * @return void
     */
    public function __construct(protected Exporter $exporter)
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
        $languages = explode(',', $this->argument('lang'));

        foreach ($languages as $language) {
            $this->exporter->export($language);

            $this->info('Translatable strings have been extracted and written to the ' . $language . '.json file.');
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
                'A language code or a comma-separated list of language codes for which the translatable strings are extracted, e.g. "es" or "es,bg,de".',
            ],
        ];
    }
}
