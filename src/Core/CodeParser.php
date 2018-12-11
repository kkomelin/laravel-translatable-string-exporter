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
     * Translation dom nodes
     *
     * @var array
     */
    protected $dom_nodes;
    
    /**
     * Translation function pattern.
     *
     * @var string
     */
    protected $pattern = '/([FUNCTIONS])\([\'"](.+)[\'"][\),]|<([DOMNODES])>(.+)<\/([DOMNODES])>/U';


    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->functions = config('laravel-translatable-string-exporter.functions',
           [
               '__',
               '_t',
               '@lang',
           ]);
        $this->pattern = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);

        $this->dom_nodes = config('laravel-translatable-string-exporter.dom-nodes',
            [
                'lang',
            ]);
        $this->pattern = str_replace('[DOMNODES]', implode('|', $this->dom_nodes), $this->pattern);
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

        // Functions: match[2] | DOM-Nodes: match[4]
        foreach (array_merge($matches[2], $matches[4]) as $string) {
            if ($string == "") {
                continue;
            }
            $strings[] = $string;
        }

        // Remove duplicates.
        $strings = array_unique($strings);

        return $strings;
    }
}
