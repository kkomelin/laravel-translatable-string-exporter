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
    protected array $directories;

    /**
     * Directories to exclude from search.
     *
     * @var array
     */
    protected array $excludedDirectories;

    /**
     * File patterns to search for.
     *
     * @var array
     */
    protected array $patterns;

    /**
     * Finder constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->directories = config(
            'laravel-translatable-string-exporter.directories',
            [
                'app',
                'resources',
            ]
        );
        $this->excludedDirectories = config(
            'laravel-translatable-string-exporter.excluded-directories',
            []
        );
        $this->patterns = config(
            'laravel-translatable-string-exporter.patterns',
            [
                '*.php',
                '*.js',
            ]
        );
    }

    /**
     * Find all files that can contain translatable strings.
     *
     * @return \Symfony\Component\Finder\Finder|null
     */
    public function find()
    {
        $path = base_path();

        $directories = $this->directories;
        array_walk($directories, function (&$item) use ($path) {
            $item = $path . DIRECTORY_SEPARATOR . $item;
        });

        $excludedDirectories = $this->excludedDirectories;

        $finder = new Finder();

        $finder = $finder->in($directories);
        $finder = $finder->exclude($excludedDirectories);

        foreach ($this->patterns as $pattern) {
            $finder->name($pattern);
        }

        return $finder->files();
    }
}
