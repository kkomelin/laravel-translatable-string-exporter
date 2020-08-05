<?php

namespace KKomelin\TranslatableStringExporter\Core;

class CodeParser
{
    /**
     * Translation function names.
     *
     * @var array
     */
    protected $functions;

    /**
     * Translation function patterns template.
     *
     * @var string
     */
    protected $tpl = "/([FUNCTIONS])\(\s*(?:(?<!\\\\)\\[QUOTE])((?:[^\[QUOTE]]|\\[QUOTE])+)(?:(?<!\\\\)\\[QUOTE])\s*[\),]/U";

    /**
     * Translation function patterns.
     *
     * @var array
     */
    protected $patterns = [];

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->functions = config('laravel-translatable-string-exporter.functions', [
            '__',
            '_t',
            '@lang',
        ]);

        $this->patterns[] = str_replace(['[FUNCTIONS]', '[QUOTE]'], [implode('|', $this->functions), "'"], $this->tpl);
        $this->patterns[] = str_replace(['[FUNCTIONS]', '[QUOTE]'], [implode('|', $this->functions), '"'], $this->tpl);
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

        foreach ($this->patterns as $pattern) {
            if (preg_match_all($pattern, $file->getContents(), $matches)) {
                foreach ($matches[2] as $string) {
                    $strings[] = stripslashes($string);
                }
            }
        }

        if (empty($strings)) {
            return [];
        }

        // Remove duplicates.
        $strings = array_unique($strings);

        return $strings;
    }
}
