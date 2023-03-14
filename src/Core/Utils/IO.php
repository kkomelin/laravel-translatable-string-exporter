<?php

namespace KKomelin\TranslatableStringExporter\Core\Utils;

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
     * @param  string  $content
     * @param  string  $path
     * @return void
     */
    public static function write(string $content, string $path)
    {
        file_put_contents($path, $content . PHP_EOL);
    }

    /**
     * Read json file and convert it into an array of strings.
     *
     * @param  string  $path
     * @return string|bool
     */
    public static function read(string $path)
    {
        if (! file_exists($path)) {
            return false;
        }

        return file_get_contents($path);
    }

    /**
     * Read existing translation file for the chosen language.
     *
     * @param  string  $language_path
     * @return array
     */
    public static function readTranslationFile(string $language_path)
    {
        $content = self::read($language_path);

        return JSON::jsonDecode($content);
    }

    /**
     * Get language file path.
     *
     * @param  string  $language
     * @return string
     */
    public static function languageFilePath(string $language)
    {
        return function_exists('lang_path') ? lang_path("$language.json") : resource_path("lang/$language.json");
    }
}
