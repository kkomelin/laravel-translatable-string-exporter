<?php
return [
    // Directories to search in.
    'directories'=> [
        'app',
        'resources',
    ],

    // File Patterns to search for.
    'patterns'=> [
        '*.php',
        '*.js',
    ],

    // Indicates whether new lines are allowed in translations.
    'allow-newlines' => false,

    // Translation function names.
    // If your function name contains $ escape it using \$ .
    'functions'=> [
        '__',
        '_t',
        '@lang',
    ],

    // Indicates whether you need to sort the translations alphabetically 
    // by original strings (keys).
    // It helps navigate a translation file and detect possible duplicates.
    'sort-keys' => true,

    // Indicates whether keys from the persistent-strings file should be also added
    // to translation files automatically on export if they don't yet exist there.
    'add-persistent-strings-to-translations' => false,
];
