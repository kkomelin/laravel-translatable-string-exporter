<?php

namespace KKomelin\TranslatableStringExtractor;

class Parser
{
    /**
     * Translation function names.
     *
     * @var array
     */
    protected $functions = [
        '__',
        '_t',
    ];

    /**
     * Translation function pattern.
     *
     * @var string
     */
    protected $pattern = '/([FUNCTIONS])\([\'"](.+)[\'"][\),]/';


    /**
     * Parser constructor.
     */
    public function __construct()
    {
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
