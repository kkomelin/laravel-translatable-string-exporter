<?php

namespace KKomelin\TranslatableStringExporter\Core;

class Exporter
{
    /**
     * The target directory for translation files.
     *
     * @var array
     */
    protected $directory = 'resources/lang';

    /**
     * Extractor object.
     *
     * @var Extractor
     */
    private $extractor;

    /**
     * Parser constructor.
     */
    public function __construct()
    {
        $this->extractor = new Extractor();
    }

    /**
     * Export translatable strings to the language file.
     *
     * @param string $base_path
     * @param string $language
     * @return array
     */
    public function export($base_path, $language)
    {
        $path = $this->getExportPath($base_path, $language);

        // Extract source strings from the project directories.
        $new_strings = $this->extractor->extract();

        // Read existing translation file for chosen language.
        $content = IO::read($path);
        $existing_strings = $this->jsonDecode($content);

        // Merge old an new translations. We don't override old strings to preserve existing translations.
        $resulting_strings = $this->mergeStrings($new_strings, $existing_strings);

        // Sort the translations if enabled through the config.
        $sorted_strings = $this->sortIfEnabled($resulting_strings);

        // Prepare JSON string and dump it to the translation file.
        $content = $this->jsonEncode($sorted_strings);
        IO::write($content, $path);
    }

    /**
     * Convert an array/object to the properly formatted JSON string.
     *
     * @param $strings
     * @return string
     */
    protected function jsonEncode($strings)
    {
        return json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Convert a JSON string to an array.
     *
     * @param string $string
     * @return array
     */
    protected function jsonDecode($string)
    {
        return (array) json_decode($string);
    }

    /**
     * Generate full target path for the resulting translation file.
     *
     * @param string $base_path
     * @param string $language
     * @return string
     */
    protected function getExportPath($base_path, $language)
    {
        return $base_path . DIRECTORY_SEPARATOR .
            $this->directory . DIRECTORY_SEPARATOR . $language . '.json';
    }

    /**
     * Merge two arrays of translations and convert the resulting array to object.
     * We don't override old strings to preserve existing translations.
     *
     * @param array $existing_strings
     * @param array $new_strings
     * @return string
     */
    protected function mergeStrings($new_strings, $existing_strings)
    {
        return array_intersect_key(array_merge($new_strings, $existing_strings), $new_strings);
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
            return array_sort($strings, function ($value, $key) {
                return strtolower($key);
            });
        }

        return $strings;
    }
}
