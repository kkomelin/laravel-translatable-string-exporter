<?php

namespace KKomelin\TranslatableStringExporter\Core\Utils;

class Misc
{
    /**
     * Check whether current version of Laravel is greater or equal to 9.
     *
     * @return bool
     */
    public static function isLaravel9OrAbove()
    {
        return version_compare(app()->version(), '9', '>=');
    }
}
