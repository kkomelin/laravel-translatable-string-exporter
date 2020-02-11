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

    // Indicates weather new lines are allowed in translations.
    'allow-newlines' => false,

    // Translation function names.
    // If your function name contains $ escape it using \$ .
    'functions'=> [
        '__',
        '_t',
        '@lang',
    ],

    // Indicates weather you need to sort the translations alphabetically 
    // by original strings (keys).
    // It helps navigate a translation file and detect possible duplicates.
    'sort-keys' => true,
];
