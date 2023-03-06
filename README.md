# Translatable String Exporter for Laravel

[![Tests Status Badge](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/run-tests.yml/badge.svg)](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/run-tests.yml)
[![PHPStan Status Badge](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/phpstan.yml.yml/badge.svg)](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/phpstan.yml.yml)
[![Code Styles Check Badge](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/php-cs-fixer.yml/badge.svg)](https://github.com/kkomelin/laravel-translatable-string-exporter/actions/workflows/php-cs-fixer.yml)

You can use `__('Translate me')` or `@lang('Translate me')` with translations in JSON files to translate strings.
Translatable String Exporter is aimed to collect all translatable strings of an application and create corresponding translation files in JSON format to simplify the process of translation.

## Versions

| Package    | PHP          |
| ---------- | ------------ |
| `<=1.15.1` | `5.6`        |
| `>1.15.1`  | `^7.2\|^8.0` |
| `>1.18.0`  | `^8.0`       |

_Even though we drop support for PHP versions in minor releases, Composer ensures that users with previous versions of PHP don't get not-yet-supported PHP code._

## Installation

_Normally, it's enough to install the package as a development dependency._

```bash
composer require kkomelin/laravel-translatable-string-exporter --dev
```

## Configuration

To change [project defaults](https://github.com/kkomelin/laravel-translatable-string-exporter/wiki/Configuration-and-Project-Defaults), use the following command to create a configuration file in your `config/` folder and make necessary changes in there:

```bash
php artisan vendor:publish --provider="KKomelin\TranslatableStringExporter\Providers\ExporterServiceProvider"
```

## Usage

### Export translatable strings

```bash
php artisan translatable:export <lang>
```

Where `<lang>` is a language code or a comma-separated list of language codes.
For example:

```bash
php artisan translatable:export es
php artisan translatable:export es,bg,de
```

The command with the `"es,bg,de"` parameter passed will create `es.json`, `bg.json`, `de.json` files with translatable strings or update the existing files in the `lang/` folder of your project.

### Find untranslated strings in a language file (command)

To inspect an existing language file (find untranslated strings), use this command:

```bash
php artisan translatable:inspect-translations fr
```

The command only supports inspecting one language at a time.

To export translatable strings for a language and then inspect translations in it, use the following command:

```bash
php artisan translatable:inspect-translations fr --export-first
```

### Find untranslated strings in a language file (IDE)

An alternative way to find untranslated strings in your language files is to search for entries with the same string for original and translated.
You can do this in most editors using a regular expression.

In PhpStorm and VSCode, you can use this pattern: `"([^"]*)": "\1"`

### Persistent strings

Some strings are not included in the export, because they are being dynamically generated. For example:

`{{ __(sprintf('Dear customer, your order has been %s', $orderStatus)) }}`

Where `$orderStatus` can be `'approved'`, `'paid'`, `'cancelled'` and so on.

In this case, you can add the strings to the `<lang>.json` file manually. For example:

```
  ...,
  "Dear customer, your order has been approved": "Dear customer, your order has been approved",
  "Dear customer, your order has been paid": "Dear customer, your order has been paid",
  ...
```

In order for those, manually added, strings not to get removed the next time you run the export command, you should add them to a json file named `persistent-strings.json`. For example:

```
[
  ...,
  "Dear customer, your order has been approved",
  "Dear customer, your order has been paid",
  ...
]
```

## License & Copyright

[MIT](https://github.com/kkomelin/laravel-translatable-string-exporter/blob/master/LICENSE), (c) 2017-present Konstantin Komelin and [contributors](https://github.com/kkomelin/laravel-translatable-string-exporter/graphs/contributors)
