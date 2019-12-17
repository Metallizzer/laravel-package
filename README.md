# Laravel package generator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/metallizzer/laravel-package.svg?style=flat-square)](https://packagist.org/packages/metallizzer/laravel-package)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/metallizzer/laravel-package.svg?style=flat-square)](https://packagist.org/packages/metallizzer/laravel-package)

This Laravel package creates new package skeleton and optionaly can make some of necessary classes for this newly created package.

## Installation

You can install the package via composer:

```bash
composer require --dev metallizzer/laravel-package
```

## Usage

To create a new package, run this artisan command:

``` bash
php artisan make:package vendor/package_name
```

You can also create necessary classes by running one of this commands:

``` bash
php artisan package:make:model ModelName
php artisan package:make:controller ControllerName
...
```

All available commands you can view by running:

``` bash
php artisan list package:make
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Oleg Petrov](https://github.com/Metallizzer)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
