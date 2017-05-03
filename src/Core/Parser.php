<?php

namespace KKomelin\TranslatableStringExporter\Core;

class Parser
{
    /**
     * Translation function names.
     *
     * @var array
     */
    protected $functions;

    /**
     * Translation function pattern.
     *
     * @var string
     */
    protected $pattern = '/([FUNCTIONS])\([\'"](.+)[\'"][\),]/U';


    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->functions = config('laravel-translatable-string-exporter.functions',
           [
                '__',
                '_t'
           ]);
        $this->pattern = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);
    }

    /**
     * Parse a file in order to find translatable strings.
     *
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return array
     */
    public function parse(\Symfony\Component\Finder\SplFileInfo $file)
    {
        $strings = [];

        if(!preg_match_all($this->pattern, $file->getContents(), $matches)) {
            return $strings;
        }

        foreach ($matches[2] as $string) {
            $strings[] = $string;
        }

        // Remove duplicates.
        $strings = array_unique($strings);

        return $strings;
    }
}
