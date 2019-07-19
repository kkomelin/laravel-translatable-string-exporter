<?php

namespace KKomelin\TranslatableStringExporter\Core;

use KKomelin\TranslatableStringExporter\Core\Utils\IO;

class UntranslatedStringFinder
{
    /**
     * Find untranslatable strings in a language file.
     *
     * @param string $base_path
     * @param string $language
     * @return array
     */
    public function find($base_path, $language)
    {
        $language_path = IO::languageFilePath($base_path, $language);

        if (!file_exists($language_path)) {
            return false;
        }

        // Read existing translation file for the chosen language.
        $existing_strings = IO::readTranslationFile($language_path);

        return array_keys(
            array_filter(
                $existing_strings,
                function ($key, $value) {
                    return $key === $value;
                },
                ARRAY_FILTER_USE_BOTH
            )
        );
    }
}
