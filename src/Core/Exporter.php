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
     * @param string $language
     * @return array
     */
    public function export($language)
    {
        $strings = $this->extractor->extract();

        $json = $this->formatJson($strings);

        $this->write($json, $language);
    }

    /**
     * Write a JSON string to the language file.
     *
     * @param string $language
     * @param $json
     */
    protected function write($json, $language) {
        $path = $this->getExportPath($language);

        file_put_contents($path, $json);
    }

    /**
     * Convert an array to the properly formatted JSON string.
     *
     * @param array $strings
     * @return string
     */
    protected function formatJson(array $strings) {

        $result = [];
        foreach ($strings as $string) {
            $result[$string] = $string;
        }

        return json_encode($result);
    }

    /**
     * Generate full target path for the resulting translation file.
     *
     * @param string $language
     * @return string
     */
    protected function getExportPath($language) {
        return base_path() . DIRECTORY_SEPARATOR . $this->directory . DIRECTORY_SEPARATOR .
            $language . '.json';
    }
}
