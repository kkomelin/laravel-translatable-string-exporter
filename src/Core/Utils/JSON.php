<?php

namespace KKomelin\TranslatableStringExporter\Core\Utils;

class JSON
{
    /**
     * Convert an array/object to the properly formatted JSON string.
     *
     * @param array $strings
     * @return string
     */
    public static function jsonEncode($strings)
    {
        return json_encode($strings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Convert a JSON string to an array.
     *
     * @param string $string
     * @return array
     */
    public static function jsonDecode($string)
    {
        return (array) json_decode($string);
    }
}
