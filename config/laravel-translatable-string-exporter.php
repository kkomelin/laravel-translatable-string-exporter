<?php
return [
    // Directories to search in.
    'directories'=> [
        'app',
        'resources'
    ],

    // File Patterns to search for.
    'patterns'=> [
        '*.php',
        '*.js'
    ],

    // Translation function names.
    // If your function name contains $ escape it using \$
    'functions'=> [
        '__',
        '_t',
        '@lang'
    ],

    // Indicates if we need to sort the translation keys
    // It might be useful to find translations and detect duplications
    'sort-keys' => false,
];
