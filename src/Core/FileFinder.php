<?php

namespace KKomelin\TranslatableStringExporter\Core;

use Symfony\Component\Finder\Finder;

class FileFinder
{
    /**
     * Directories to search in.
     *
     * @var array
     */
    protected $directories;

    /**
     * File patterns to search for.
     *
     * @var array
     */
    protected $patterns;

    /**
     * Finder constructor.
     */
    public function __construct()
    {
        $this->directories = config('laravel-translatable-string-exporter.directories',
            [
                'app',
                'resources',
            ]);
        $this->patterns = config('laravel-translatable-string-exporter.patterns',
            [
                '*.php',
                '*.js'
            ]);
    }

    /**
     * Find all files that can contain translatable strings.
     *
     * @return Finder|null
     */
    public function find()
    {
        $path = base_path();

        $directories = $this->directories;
        array_walk($directories, function (&$item) use($path) {
            $item = $path . DIRECTORY_SEPARATOR . $item;
        });

        $finder = new Finder();

        $finder = $finder->in($directories);

        foreach ($this->patterns as $pattern) {
            $finder->name($pattern);
        }

        return $finder->files();
    }
}
