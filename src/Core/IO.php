<?php

namespace KKomelin\TranslatableStringExporter\Core;

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
}
