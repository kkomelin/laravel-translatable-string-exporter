<?php

namespace KKomelin\TranslatableStringExporter\Core\Utils;

use KKomelin\TranslatableStringExporter\Core\Utils\JSON;

/**
 * Class IO is responsible for reading from and writing to files.
 *
 * @package KKomelin\TranslatableStringExporter\Core
 */
class IO
{
    /**
     * The target directory for translation files.
     *
     * @var string
     */
    const TRANSLATION_FILE_DIRECTORY = 'resources/lang';

    /**
     * Write a string to a file.
     *
     * @param string $path
     * @param $content
     */
    public static function write($content, $path) {
        file_put_contents($path, $content);
    }

    /**
     * Read json file and convert it into an array of strings.
     *
     * @return string|bool
     */
    public static function read($path) {

        if (!file_exists($path)) {
            return false;
        }

        return file_get_contents($path);
    }

    /**
     * Read existing translation file for the chosen language.
     *
     * @param $language_path
     * @return array
     */
    public static function readTranslationFile($language_path) {
        $content = self::read($language_path);
        return JSON::jsonDecode($content);
    }

    /**
     * Get language file path.
     *
     * @param string $base_path
     * @param string $language
     * @return string
     */
    public static function languageFilePath($base_path, $language)
    {
        return $base_path . DIRECTORY_SEPARATOR .
            self::TRANSLATION_FILE_DIRECTORY . DIRECTORY_SEPARATOR .
            $language . '.json';
    }
}
