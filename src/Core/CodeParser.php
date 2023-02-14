<?php

namespace KKomelin\TranslatableStringExporter\Core;

use Symfony\Component\Finder\SplFileInfo;

class CodeParser
{
	/**
	 * Translation function names.
	 *
	 * @var array
	 */
	protected $functions;

	/**
	 * Translation function pattern.
	 *
	 * @var string|array
	 */
	protected $pattern = '/([FUNCTIONS])\(\s*([\'"])(?P<string>(?:(?![^\\\]\2).)+.)\2\s*[\),]/u';

	/**
	 * Parser constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->functions = config(
			'laravel-translatable-string-exporter.functions',
			[
				'__',
				'_t',
				'@lang',
			]
		);
		if (\Arr::isAssoc($this->functions)) {
			$patterns = [];
			foreach ($this->functions as $key => $value) {
				if (\is_numeric($key)) {
					$func     = $value;
					$callable = null;
				} else {
					$func     = $key;
					$callable = $value;
				}
				$patterns[str_replace('[FUNCTIONS]', $func, $this->pattern)] = $callable;

				if (config('laravel-translatable-string-exporter.allow-newlines', false)) {
					$patterns[$key] .= 's';
				}
			}
			$this->pattern = $patterns;
		} else {
			$this->pattern = str_replace('[FUNCTIONS]', implode('|', $this->functions), $this->pattern);

			if (config('laravel-translatable-string-exporter.allow-newlines', false)) {
				$this->pattern .= 's';
			}
		}
	}

	/**
	 * Parse a file in order to find translatable strings.
	 *
	 * @return array
	 */
	public function parse(SplFileInfo $file)
	{
		$strings = [];

		if (\is_array($this->pattern)) {
			foreach ($this->pattern as $pattern => $func) {
				preg_match_all($pattern, $file->getContents(), $matches);

				foreach ($matches['string'] as $string) {
					$strings[] = is_callable($func) ? $func($string) : $string;
				}
			}

			// Remove duplicates.
			$strings = array_unique($strings);

			return $this->clean($strings);
		} else {
			if (!preg_match_all($this->pattern, $file->getContents(), $matches)) {
				return $this->clean($strings);
			}

			foreach ($matches['string'] as $string) {
				$strings[] = $string;
			}

			// Remove duplicates.
			$strings = array_unique($strings);

			return $this->clean($strings);
		}
	}

	/**
	 * Provide extra clean up step
	 * Used for instances of {{ __('We\'re amazing!') }}
	 * Without clean up: We\'re amazing!
	 * With clean up: We're amazing!
	 *
	 * @return array
	 */
	public function clean(array $strings)
	{
		return array_map(function ($string) {
			return str_replace('\\\'', '\'', $string);
		}, $strings);
	}
}
