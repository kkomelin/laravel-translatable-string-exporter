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
     * Write a string to a file.
     *
     * @param string $content
     * @param string $path
     * @return void
     */
    public static function write($content, $path) {
        file_put_contents($path, $content);
    }

    /**
     * Read json file and convert it into an array of strings.
     *
     * @param string $path
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
     * @param string $language_path
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
        $translation_file_directory =
            is_dir($base_path . DIRECTORY_SEPARATOR . 'resources/lang') 
            ? 'resources/lang' 
            : 'lang';

        return $base_path . DIRECTORY_SEPARATOR .
            $translation_file_directory . DIRECTORY_SEPARATOR .
            $language . '.json';
    }
}
