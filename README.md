# Parallel Integration Tests in Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tonysm/dbcreatecmd.svg?style=flat-square)](https://packagist.org/packages/tonysm/dbcreatecmd)
[![Build Status](https://img.shields.io/travis/tonysm/dbcreatecmd/master.svg?style=flat-square)](https://travis-ci.org/tonysm/dbcreatecmd)
[![Quality Score](https://img.shields.io/scrutinizer/g/tonysm/dbcreatecmd.svg?style=flat-square)](https://scrutinizer-ci.com/g/tonysm/dbcreatecmd)
[![Total Downloads](https://img.shields.io/packagist/dt/tonysm/dbcreatecmd.svg?style=flat-square)](https://packagist.org/packages/tonysm/dbcreatecmd)

This package ships with some helper Artisan commands and testing traits to allow you running your Feature Tests in parallel using [Paratest](https://github.com/paratestphp/paratest) against a MySQL or PostgreSQL database without conflicts.

The package will create 1 database for each testing process you have running to avoid race conditions when your Feature Test try to run a test creating some fixtures while another test in a another process runs the `artisan migrate:fresh`.

You also don't have to worry about creating the test databases. They will be created when you run your tests. There's is even a helper runner to clean up the test databases afterwards.

## Installation

You can install the package via composer:

```bash
composer require tonysm/dbcreatecommand
```

## Usage

**Attention: You will need a user with rights to create databases.**

Instead of using Laravel's _RefreshDatabase_ trait, use the package one:

```php
<?php

use Tonysm\DbCreateCommand\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;
}
```

Tip: to replace all existing usages of Laravel's RefreshDatabase trait with the package's, you can use the following command:

```bash
grep -rl 'Illuminate\\Foundation\\Testing\\RefreshDatabase' tests/ | xargs sed -i 's/Illuminate\\Foundation\\Testing\\RefreshDatabase/Tonysm\\DbCreateCommand\\Testing\\RefreshDatabase/g'
```

You need to boot this setup trait in your base TestCase manually, because Laravel does not do it automatically:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tonysm\DbCreateCommand\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[RefreshDatabase::class])) {
            $this->refreshDatabase();
        }

        return $uses;
    }
}
```

You can keep running you tests with PHPUnit:

``` php
phpunit
```

Or you can use Paratest:

``` php
paratest
```

When using paratest, one database will be created for each process. If you want to clean up these databases at the end of the tests, use the runner provided. First, register the runner alias in your `composer.json` file, something like this:

```json
{
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        },
        "files": [
            "vendor/tonysm/dbcreatecmd/src/ParatestLaravelRunner.php"
        ]
    }
}
```

Now, run `composer dump -o`, and then you can use the runner, like so:

```php
paratest --runner ParatestLaravelRunner
```

This will clean up the test databases after your test finishes running.

This package also gives you the following Artisan commands:

- `php artisan db:create`
- `php artisan db:drop`
- `php artisan db:recreate`

Use it wisely.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email tonyzrp@gmail.com instead of using the issue tracker.

## Credits

- [Tony Messias](https://github.com/tonysm)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
