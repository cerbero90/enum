# 🎲 Enum

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PSR-12][ico-psr12]][link-psr12]
[![Total Downloads][ico-downloads]][link-downloads]

Zero-dependencies PHP library to supercharge enum functionalities. Similar libraries worth mentioning are:
- [Enum Helper](https://github.com/datomatic/enum-helper) by [Alberto Peripolli](https://github.com/trippo)
- [Enums](https://github.com/archtechx/enums) by [Samuel Štancl](https://github.com/stancl)


## 📦 Install

Via Composer:

``` bash
composer require cerbero/enum
```

## 🔮 Usage

* [Classification](#classification)
* [Comparison](#comparison)
* [Keys resolution](#keys-resolution)
* [Hydration](#hydration)
* [Cases collection](#cases-collection)

To supercharge our enums with all functionalities provided by this package, we can simply use the `Enumerates` trait in both pure enums and backed enums:

```php
use Cerbero\Enum\Concerns\Enumerates;

enum PureEnum
{
    use Enumerates;

    case one;
    case two;
    case three;
}

enum BackedEnum: int
{
    use Enumerates;

    case one = 1;
    case two = 2;
    case three = 3;
}
```


### Classification

These methods determine whether an enum is pure or backed:

```php
PureEnum::isPure(); // true
PureEnum::isBacked(); // false

BackedEnum::isPure(); // false
BackedEnum::isBacked(); // true
```


### Comparison

We can check whether an enum includes some names or values. Pure enums check for names, whilst backed enums check for values:

```php
PureEnum::has('one'); // true
PureEnum::has('four'); // false
PureEnum::doesntHave('one'); // false
PureEnum::doesntHave('four'); // true

BackedEnum::has(1); // true
BackedEnum::has(4); // false
BackedEnum::doesntHave(1); // false
BackedEnum::doesntHave(4); // true
```

Otherwise we can let cases determine whether they match with a name or a value:

```php
PureEnum::one->is('one'); // true
PureEnum::one->is(1); // false
PureEnum::one->is('four'); // false
PureEnum::one->isNot('one'); // false
PureEnum::one->isNot(1); // true
PureEnum::one->isNot('four'); // true

BackedEnum::one->is(1); // true
BackedEnum::one->is('1'); // false
BackedEnum::one->is(4); // false
BackedEnum::one->isNot(1); // false
BackedEnum::one->isNot('1'); // true
BackedEnum::one->isNot(4); // true
```

Comparisons can also be performed within arrays:

```php
PureEnum::one->in(['one', 'four']); // true
PureEnum::one->in([1, 4]); // false
PureEnum::one->notIn('one', 'four'); // false
PureEnum::one->notIn([1, 4]); // true

BackedEnum::one->in([1, 4]); // true
BackedEnum::one->in(['one', 'four']); // false
BackedEnum::one->notIn([1, 4]); // false
BackedEnum::one->notIn(['one', 'four']); // true
```


### Keys resolution

With the term "key" we refer to any element defined in an enum, such as names, values or methods implemented by cases. Take the following enum for example:

```php
enum BackedEnum: int
{
    use Enumerates;

    case one = 1;
    case two = 2;
    case three = 3;

    public function color(): string
    {
        return match ($this) {
            static::one => 'red',
            static::two => 'green',
            static::three => 'blue',
        };
    }

    public function isOdd(): bool
    {
        return match ($this) {
            static::one => true,
            static::two => false,
            static::three => true,
        };
    }
}
```

The keys defined in this enum are `name`, `value` (as it is a backed enum), `color`, `isOdd`. We can retrieve any key assigned to a case by calling `get()`:

```php
PureEnum::one->get('name'); // 'one'
PureEnum::one->get('value'); // throws ValueError as it is a pure enum
PureEnum::one->get('color'); // 'red'
PureEnum::one->get(fn (PureEnum $caseOne) => $caseOne->isOdd()); // true

BackedEnum::one->get('name'); // 'one'
BackedEnum::one->get('value'); // 1
BackedEnum::one->get('color'); // 'red'
BackedEnum::one->get(fn (BackedEnum $caseOne) => $caseOne->isOdd()); // true
```

At first glance this method may seem an overkill as "keys" can be accessed directly by cases like this:

```php
BackedEnum::one->name; // 'one'
BackedEnum::one->value; // 1
BackedEnum::one->color(); // 'red'
BackedEnum::one->isOdd(); // true
```

However `get()` is useful to resolve keys dynamically as a key may be a property, a method or a closure. It also gets called internally for more advanced functionalities that we are going to explore very soon.


### Hydration

An enum case can be instantiated from its own name, value (if backed) and [keys](#keys-resolution):

```php
PureEnum::from('one'); // PureEnum::one
PureEnum::from('four'); // throws ValueError
PureEnum::tryFrom('one'); // PureEnum::one
PureEnum::tryFrom('four'); // null
PureEnum::fromName('one'); // PureEnum::one
PureEnum::fromName('four'); // throws ValueError
PureEnum::tryFromName('one'); // PureEnum::one
PureEnum::tryFromName('four'); // null
PureEnum::fromKey('name', 'one'); // PureEnum::one
PureEnum::fromKey('value', 1); // throws ValueError
PureEnum::fromKey('color', 'red'); // PureEnum::one
PureEnum::fromKey(fn (PureEnum $case) => $case->isOdd(), true); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::tryFromKey('name', 'one'); // PureEnum::one
PureEnum::tryFromKey('value', 1); // null
PureEnum::tryFromKey('color', 'red'); // PureEnum::one
PureEnum::tryFromKey(fn (PureEnum $case) => $case->isOdd(), true); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::fromColor('red'); // PureEnum::one
PureEnum::fromInvalid('invalid'); // throws ValueError
PureEnum::fromIsOdd(); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::tryFromColor('red'); // PureEnum::one
PureEnum::tryFromInvalid('invalid'); // null
PureEnum::tryFromIsOdd(); // CasesCollection<PureEnum::one, PureEnum::three>

BackedEnum::from(1); // BackedEnum::one
BackedEnum::from('1'); // throws ValueError
BackedEnum::tryFrom(1); // BackedEnum::one
BackedEnum::tryFrom('1'); // null
BackedEnum::fromName('one'); // BackedEnum::one
BackedEnum::fromName('four'); // throws ValueError
BackedEnum::tryFromName('one'); // BackedEnum::one
BackedEnum::tryFromName('four'); // null
BackedEnum::fromKey('name', 'one'); // BackedEnum::one
BackedEnum::fromKey('value', 1); // BackedEnum::one
BackedEnum::fromKey('color', 'red'); // BackedEnum::one
BackedEnum::fromKey(fn (BackedEnum $case) => $case->isOdd(), true); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::tryFromKey('name', 'one'); // BackedEnum::one
BackedEnum::tryFromKey('value', 1); // BackedEnum::one
BackedEnum::tryFromKey('color', 'red'); // BackedEnum::one
BackedEnum::tryFromKey(fn (BackedEnum $case) => $case->isOdd(), true); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::fromColor('red'); // BackedEnum::one
BackedEnum::fromInvalid('invalid'); // throws ValueError
BackedEnum::fromIsOdd(); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::tryFromColor('red'); // BackedEnum::one
BackedEnum::tryFromInvalid('invalid'); // null
BackedEnum::tryFromIsOdd(); // CasesCollection<BackedEnum::one, BackedEnum::three>
```

## 📆 Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## 🧪 Testing

``` bash
composer test
```

## 💞 Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## 🧯 Security

If you discover any security related issues, please email andrea.marco.sartori@gmail.com instead of using the issue tracker.

## 🏅 Credits

- [Andrea Marco Sartori][link-author]
- [All Contributors][link-contributors]

## ⚖️ License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-author]: https://img.shields.io/static/v1?label=author&message=cerbero90&color=50ABF1&logo=twitter&style=flat-square
[ico-php]: https://img.shields.io/packagist/php-v/cerbero/enum?color=%234F5B93&logo=php&style=flat-square
[ico-version]: https://img.shields.io/packagist/v/cerbero/enum.svg?label=version&style=flat-square
[ico-actions]: https://img.shields.io/github/workflow/status/cerbero90/enum/build?style=flat-square&logo=github
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-psr12]: https://img.shields.io/static/v1?label=compliance&message=PSR-12&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/enum.svg?style=flat-square

[link-author]: https://twitter.com/cerbero90
[link-php]: https://www.php.net
[link-packagist]: https://packagist.org/packages/cerbero/enum
[link-actions]: https://github.com/cerbero90/enum/actions?query=workflow%3Abuild
[link-psr12]: https://www.php-fig.org/psr/psr-12/
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/enum/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/enum
[link-downloads]: https://packagist.org/packages/cerbero/enum
[link-contributors]: ../../contributors
