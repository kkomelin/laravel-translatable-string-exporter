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
        // @todo: Refactor this method in order to separate concerns.

        $new_strings = $this->extractor->extract();

        $new_strings = $this->formatArray($new_strings);

        $path = $this->getExportPath($base_path, $language);

        $existing_strings = $this->read($path);

        $resulting_strings = (object) array_merge($new_strings, $existing_strings);

        $json = $this->formatJson($resulting_strings);

        $this->write($json, $path);
    }

    /**
     * Write a string to a file.
     *
     * @todo: Extract the functio to a separate IO class.
     *
     * @param string $path
     * @param $content
     */
    protected function write($content, $path) {
        file_put_contents($path, $content);
    }

    /**
     * Read json file and convert it into an array of strings.
     *
     * @todo: Extract the functio to a separate IO class.
     *
     * @return array
     */
    protected function read($path) {

        if (!file_exists($path)) {
            return [];
        }

        $content = file_get_contents($path);

        return (array) json_decode($content);
    }

    /**
     * Convert an array/object to the properly formatted JSON string.
     *
     * @param $strings
     * @return string
     */
    protected function formatJson($strings) {
        return json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Convert an array of extracted strings to an associative array where each string becomes key and value.
     *
     * @param array $strings
     * @return array
     */
    protected function formatArray(array $strings) {

        $result = [];

        foreach ($strings as $string) {
            $result[$string] = $string;
        }

        return $result;
    }

    /**
     * Generate full target path for the resulting translation file.
     *
     * @param string $base_path
     * @param string $language
     * @return string
     */
    protected function getExportPath($base_path, $language) {
        return $base_path . DIRECTORY_SEPARATOR .
            $this->directory . DIRECTORY_SEPARATOR . $language . '.json';
    }
}
