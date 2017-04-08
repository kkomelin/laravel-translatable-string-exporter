<?php

namespace KKomelin\TranslatableStringExtractor;

class Exporter
{
    /**
     * The target directory for translation files.
     *
     * @var array
     */
    protected $directory = 'resource/lang';

    /**
     * The target language code.
     *
     * @var string
     */
    private $language;

    /**
     * Full target path of the language file.
     *
     * @var string
     */
    private $path;

    /**
     * Parser constructor.
     */
    public function __construct($language)
    {
        $this->language = $language;
        $this->path = $this->getExportPath();
    }

    /**
     * Parse a file in order to find translatable strings.
     *
     * @param array $strings
     * @return array
     */
    public function export(array $strings)
    {
        $json = json_encode($strings);

        file_put_contents($this->path, $json);
    }

    /**
     * Generate full target path for the resulting translation file.
     *
     * @return string
     */
    private function getExportPath() {
        return base_path() . DIRECTORY_SEPARATOR . $this->directory . DIRECTORY_SEPARATOR .
            $this->language . '.json';
    }
}
