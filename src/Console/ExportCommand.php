<?php
namespace KKomelin\TranslatableStringExporter\Console;

use Illuminate\Console\Command;
use KKomelin\TranslatableStringExporter\Core\Exporter;
use KKomelin\TranslatableStringExporter\Core\Extractor;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command {

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
     * @var Extractor
     */
    protected $exporter;

    /**
     * ExtractCommand constructor.
     *
     * @param Extractor $extractor
     */
    public function __construct(Exporter $exporter)
    {
        parent::__construct();
        
        $this->exporter = $exporter;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $language = $this->argument('lang');

        $this->exporter->export(base_path(), $language);

        $this->info('Translatable strings have been extracted and written to the ' . $language . '.json file.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('lang', InputArgument::REQUIRED, 'A language code for which the translatable strings are extracted, e.g. "es".'),
        );
    }
}
