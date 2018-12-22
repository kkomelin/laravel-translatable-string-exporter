# Translatable String Exporter for Laravel >= 5.4

As we know, Laravel 5.4 has introduced a "new" way of string translation.
Now you can use `__('Translate me')` or `@lang('Translate me')` with translations in JSON files to translate strings.
Translatable String Exporter is aimed to collect all translatable strings of an application and create corresponding translation files in JSON format to simplify the process of translation.

## Installation

1. Add kkomelin/laravel-translatable-string-exporter to your project:

```bash
composer require kkomelin/laravel-translatable-string-exporter
```

2. For **Laravel >= 5.5** we use Package Auto-Discovery, so you may skip this step.
   For **Laravel < 5.5**, add `ExporterServiceProvider` to the providers array in config/app.php:

```php
KKomelin\TranslatableStringExporter\Providers\ExporterServiceProvider::class,
```

## Configuration

To change [project defaults](https://github.com/kkomelin/laravel-translatable-string-exporter/wiki/Configuration-and-Project-Defaults), use the following command to create a configuration file in your `config` folder and make necessary changes in there:

```bash
php artisan vendor:publish --provider="KKomelin\TranslatableStringExporter\Providers\ExporterServiceProvider"
```

## Usage

```bash
php artisan translatable:export <lang>
```

Where `<lang>` is a language code or a comma-separated list of language codes.  
For example:
```bash
php artisan translatable:export es
php artisan translatable:export es,bg,de
```

The command with the "es,bg,de" parameter passed will create es.json, bg.json, de.json files with translatable strings or update the existing files in the `resources/lang` folder of your project.

### Find untranslated strings in a language file

The easiest way to find untranslated strings in your language files at the moment is to search for entries with the same string for original and translated. You can do this in most editors using a regular expression.

In PhpStorm, you can use this pattern: `"([^"]*)": "\1"`

## License & Copyright

MIT, (c) 2018 Konstantin Komelin
