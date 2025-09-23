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
     * Base search pattern.
     *
     * @var string
     */
    protected $basePattern = '/([FUNCTIONS])\(\s*([\'"])(?P<string>(?:(?![^\\\]\2).)+.)\2\s*[\),]/u';

    /**
     * Named parameter search pattern for functions like trans_choice(key: 'string', ...).
     *
     * @var string
     */
    protected $namedParamPattern = '/([FUNCTIONS])\(\s*key:\s*([\'"])(?P<string>(?:(?![^\\\]\2).)+.)\2/u';

    /**
     * Function-specific search patterns.
     *
     * @var array
     */
    protected $patterns = [];

    /**
     * Parser constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->functions = config(
            'laravel-translatable-string-exporter.functions',
            [
                '__',
                'trans_choice',
                '_t',
                '@lang',
            ]
        );

        foreach ($this->functions as $key => $value) {
            if (\is_numeric($key)) {
                $func = $value;
                $callable = null;
            } else {
                $func = $key;
                $callable = $value;
            }

            // Add base pattern.
            $pattern_key = str_replace('[FUNCTIONS]', $func, $this->basePattern);
            if (config('laravel-translatable-string-exporter.allow-newlines', false)) {
                $pattern_key .= 's';
            }
            $this->patterns[$pattern_key] = $callable;

            // Add named parameter pattern for trans_choice and __ functions.
            if (in_array($func, ['trans_choice', '__'])) {
                $named_pattern = str_replace('[FUNCTIONS]', $func, $this->namedParamPattern);
                if (config('laravel-translatable-string-exporter.allow-newlines', false)) {
                    $named_pattern .= 's';
                }
                $this->patterns[$named_pattern] = $callable;
            }
        }
    }

    /**
     * Parse a file in order to find translatable strings.
     *
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @return array
     */
    public function parse(SplFileInfo $file)
    {
        $strings = [];

        foreach ($this->patterns as $pattern => $func) {
            preg_match_all($pattern, $file->getContents(), $matches);

            foreach ($matches['string'] as $string) {
                if (\is_null($func)) {
                    $strings[] = $string;
                } elseif (\is_callable($func)) {
                    $strings[] = call_user_func($func, $string);
                }
            }
        }

        // Remove duplicates.
        $strings = array_unique($strings);

        return $this->clean($strings);
    }

    /**
     * Provide extra clean up step
     * Used for instances of {{ __('We\'re amazing!') }}
     * Without clean up: We\'re amazing!
     * With clean up: We're amazing!
     *
     * @param array $strings
     * @return array
     */
    public function clean(array $strings)
    {
        return array_map(function ($string) {
            return str_replace('\\\'', '\'', $string);
        }, $strings);
    }
}
