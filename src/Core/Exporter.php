<?php

namespace KKomelin\TranslatableStringExporter\Core;

use Illuminate\Support\Arr;
use KKomelin\TranslatableStringExporter\Core\Utils\JSON;
use KKomelin\TranslatableStringExporter\Core\Utils\IO;

class Exporter
{
    /**
     * The filename without extension for persistent strings.
     *
     * @var string
     */
    const PERSISTENT_STRINGS_FILENAME_WO_EXT = 'persistent-strings';

    /**
     * Extractor object.
     *
     * @var StringExtractor
     */
    private $extractor;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->extractor = new StringExtractor();
    }

    /**
     * Export translatable strings to the language file.
     *
     * @param string $base_path
     * @param string $language
     * @param array  $patterns
     * @return array
     */
    public function export($base_path, $language, array $patterns=null)
    {
        $language_path = IO::languageFilePath($base_path, $language);

        // Extract source strings from the project directories.
        $new_strings = $this->extractor->extract($patterns);

        // Read existing translation file for the chosen language.
        $existing_strings = IO::readTranslationFile($language_path);

        // Get the persistent strings.
        $persistent_strings_path =
            IO::languageFilePath($base_path, self::PERSISTENT_STRINGS_FILENAME_WO_EXT);
        $persistent_strings = IO::readTranslationFile($persistent_strings_path);

        // Merge old an new translations preserving existing translations and persistent strings.
        $resulting_strings = $this->mergeStrings($new_strings, $existing_strings, $persistent_strings);

        // Sort the translations if enabled through the config.
        $sorted_strings = $this->sortIfEnabled($resulting_strings);

        // Prepare JSON string and dump it to the translation file.
        $content = JSON::jsonEncode($sorted_strings);
        IO::write($content, $language_path);
    }

    /**
     * Merge two arrays of translations preserving existing translations and persistent strings.
     *
     * @param array $existing_strings
     * @param array $new_strings
     * @param array $persistent_strings
     * @return array
     */
    protected function mergeStrings($new_strings, $existing_strings, $persistent_strings)
    {
        $merged_strings = array_merge($new_strings, $existing_strings);
        return $this->arrayFilterByKey($merged_strings, function ($key) use ($persistent_strings, $new_strings) {
            return in_array($key, $persistent_strings) || array_key_exists($key, $new_strings);
        });
    }

    /**
     * Sort the translation strings alphabetically by their original strings (keys) 
     * if the corresponding option is enabled through the package config.
     *
     * @param array $strings
     * @return array
     */
    protected function sortIfEnabled($strings)
    {
        if (config('laravel-translatable-string-exporter.sort-keys', false)) {
            return Arr::sort($strings, function ($value, $key) {
                return strtolower($key);
            });
        }

        return $strings;
    }

    /**
     * Filtering an array by its keys using a callback.
     * Supports PHP < 5.6.0. Use array_filter($array, $callback, ARRAY_FILTER_USE_KEY) instead
     * if you don't need to support earlier versions.
     *
     * The code borrowed from https://gist.github.com/h4cc/8e2e3d0f6a8cd9cacde8
     *
     * @deprecated 2.0.0 Replace with array_filter($array, $callback, ARRAY_FILTER_USE_KEY) when drop PHP < 5.6 support.
     *
     * @param array $array
     *  The array to iterate over.
     * @param callable $callback
     *  The callback function to use.
     *
     * @return array
     *  The filtered array.
     */
    private function arrayFilterByKey($array, $callback)
    {
        $matchedKeys = array_filter(array_keys($array), $callback);
        return array_intersect_key($array, array_flip($matchedKeys));
    }
}
