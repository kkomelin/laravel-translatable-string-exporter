<?php

namespace KKomelin\TranslatableStringExporter\Core;

use KKomelin\TranslatableStringExporter\Core\Utils\IO;

class UntranslatedStringFinder
{
    /**
     * Find untranslated strings in a language file.
     *
     * @param  string  $language
     * @return array|false
     */
    public function find(string $language)
    {
        $language_path = IO::languageFilePath($language);

        if (! file_exists($language_path)) {
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
