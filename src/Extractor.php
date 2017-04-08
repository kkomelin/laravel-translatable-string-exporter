<?php

namespace KKomelin\TranslatableStringExtractor;

class Extractor
{
    private $finder;
    private $parser;

    public function __construct()
    {
        $this->finder = new Finder();
        $this->parser = new Parser();
    }

    /**
     * Extract tanslatable strings from the project files and record them to the JSON language file.
     *
     * @param $language
     */
    public function extract($language) {

        $strings = [];

        $files = $this->finder->find();
        foreach ($files as $file) {
            $strings = array_merge($strings, $this->parser->parse($file));
        }

        return $strings;
    }
}
