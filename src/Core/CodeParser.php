<?php

namespace KKomelin\TranslatableStringExporter\Core;

use Symfony\Component\Finder\SplFileInfo;

class CodeParser
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
    protected $pattern = '/([FUNCTIONS])\(\h*[\'"](.+)[\'"]\h*[\),]/U';


    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->functions = config('laravel-translatable-string-exporter.functions',
           [
               '__',
               '_t',
               '@lang'
           ]);
        $this->pattern = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);

        if (config('laravel-translatable-string-exporter.allow-newlines', false)) {
            $this->pattern .= 's';
        }
    }

    /**
     * Parse a file in order to find translatable strings.
     *
     * @param SplFileInfo $file
     * @return array
     */
    public function parse(SplFileInfo $file)
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
