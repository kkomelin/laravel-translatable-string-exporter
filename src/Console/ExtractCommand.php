<?php
namespace KKomelin\TranslatableStringExtractor\Console;

use Illuminate\Console\Command;
use KKomelin\TranslatableStringExtractor\Extractor;
use Symfony\Component\Console\Input\InputArgument;

class ExtractCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:extract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extract translatable strings for a language to a JSON file.';


    /**
     * @var Extractor
     */
    protected $extractor;

    /**
     * ExtractCommand constructor.
     *
     * @param Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        parent::__construct();
        
        $this->extractor = $extractor;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $language = $this->argument('lang');

        $strings = $this->extractor->extract($language);

        dd($strings);

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
