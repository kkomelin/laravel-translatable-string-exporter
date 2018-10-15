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

Where `<lang>` is a language code, for example "es". You can also provide multiple languages to export to at once, separating their codes with comas "es,en,bg".

The command with the "es" parameter will create es.json file in the `resources/lang` folder of your project.

## License & Copyright

MIT, (c) 2018 Konstantin Komelin
