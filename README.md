# Translatable String Extractor for Laravel
As we know, Laravel 5.4 has introduced a "new" way of string translation.
Now you can use `__('Translate me')` functon and JSON files to translate strings.
Translatable String Extractor is aimed to collect all translatable strings of an application and create corresponding translation files in JSON formats to simplify and automate the process of translating.

Please note, the project is in its early development stage, so it can have issues which we will definitely address with your help ;)

## Installation

1) Add kkomelin/laravel-translatable-string-extractor to your project:

```bash
composer require kkomelin/laravel-translatable-string-extractor
```

2) Add the `ExtractorServiceProvider` to the providers array in config/app.php:

```
KKomelin\TranslatableStringExtractor\Providers\ExtractorServiceProvider::class,
```

## Roadmap

- [ ] Extract translatable strings and save them to a language file
- [ ] Support different translation function names
- [ ] Preserve existing translations
- [ ] An option to override existing translations
- [ ] Automated tests

## License & Copyright

MIT, (c) 2017 Konstantin Komelin
