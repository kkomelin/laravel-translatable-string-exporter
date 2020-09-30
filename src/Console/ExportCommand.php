<?php
namespace KKomelin\TranslatableStringExporter\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use KKomelin\TranslatableStringExporter\Core\Exporter;
use KKomelin\TranslatableStringExporter\Core\StringExtractor;

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
     * @var StringExtractor
     */
    protected $exporter;

    /**
     * ExtractCommand constructor.
     *
     * @param StringExtractor $extractor
     */
    public function __construct(Exporter $exporter)
    {
        parent::__construct();

        $this->exporter = $exporter;
    }

    /**
     * Execute the console command.
     *
     * @deprecated It's used to support Laravel 5.4 and below.
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $languages = explode(',', $this->argument('lang'));
        $patterns = null;

        if($this->option('patterns')) {
            $patterns = explode(',', $this->option('patterns'));
        }

        foreach ($languages as $language) {
            $this->exporter->export(base_path(), $language, $patterns);

            $this->info('Translatable strings have been extracted and written to the ' . $language . '.json file.');
        }
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
                'A language code or a comma-separated list of language codes for which the translatable strings are extracted, e.g. "es" or "es,bg,de".'
            ]
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'patterns',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Override the default patterns and use the given instead, e.g. --patterns="*.php,*.js".'
            ]
        ];
    }
}
