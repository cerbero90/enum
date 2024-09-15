# 🎲 Enum

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![PHPStan Level][ico-phpstan]][link-phpstan]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PER][ico-per]][link-per]
[![Total Downloads][ico-downloads]][link-downloads]

Zero-dependencies PHP library to supercharge enum functionalities.


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
* [Elaborating cases](#elaborating-cases)
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

The keys defined in this enum are `name`, `value` (as it is a backed enum), `color` and `isOdd`. We can retrieve any key assigned to a case by calling `get()`:

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

However `get()` is useful to resolve keys dynamically as a key may be a property, a method or a closure. It often gets called internally for more advanced functionalities that we are going to explore very soon.


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
PureEnum::fromKey('name', 'one'); // CasesCollection<PureEnum::one>
PureEnum::fromKey('value', 1); // throws ValueError
PureEnum::fromKey('color', 'red'); // CasesCollection<PureEnum::one>
PureEnum::fromKey(fn (PureEnum $case) => $case->isOdd(), true); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::tryFromKey('name', 'one'); // CasesCollection<PureEnum::one>
PureEnum::tryFromKey('value', 1); // null
PureEnum::tryFromKey('color', 'red'); // CasesCollection<PureEnum::one>
PureEnum::tryFromKey(fn (PureEnum $case) => $case->isOdd(), true); // CasesCollection<PureEnum::one, PureEnum::three>

BackedEnum::from(1); // BackedEnum::one
BackedEnum::from('1'); // throws ValueError
BackedEnum::tryFrom(1); // BackedEnum::one
BackedEnum::tryFrom('1'); // null
BackedEnum::fromName('one'); // BackedEnum::one
BackedEnum::fromName('four'); // throws ValueError
BackedEnum::tryFromName('one'); // BackedEnum::one
BackedEnum::tryFromName('four'); // null
BackedEnum::fromKey('name', 'one'); // CasesCollection<BackedEnum::one>
BackedEnum::fromKey('value', 1); // CasesCollection<BackedEnum::one>
BackedEnum::fromKey('color', 'red'); // CasesCollection<BackedEnum::one>
BackedEnum::fromKey(fn (BackedEnum $case) => $case->isOdd(), true); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::tryFromKey('name', 'one'); // CasesCollection<BackedEnum::one>
BackedEnum::tryFromKey('value', 1); // CasesCollection<BackedEnum::one>
BackedEnum::tryFromKey('color', 'red'); // CasesCollection<BackedEnum::one>
BackedEnum::tryFromKey(fn (BackedEnum $case) => $case->isOdd(), true); // CasesCollection<BackedEnum::one, BackedEnum::three>
```

While pure enums try to hydrate cases from names, backed enums can hydrate from both names and values. Even keys can be used to hydrate cases, cases are then wrapped into a [`CasesCollection`](#cases-collection) to allow further processing.


### Elaborating cases

There is a bunch of operations that can be performed on the cases of an enum. If the result of an operation is a plain list of cases, they get wrapped into a [`CasesCollection`](#cases-collection) for additional elaboration, otherwise the final result of the operation is returned:

```php
PureEnum::collect(); // CasesCollection<PureEnum::one, PureEnum::two, PureEnum::three>
PureEnum::count(); // 3
PureEnum::casesByName(); // ['one' => PureEnum::one, 'two' => PureEnum::two, 'three' => PureEnum::three]
PureEnum::casesByValue(); // []
PureEnum::casesBy('color'); // ['red' => PureEnum::one, 'green' => PureEnum::two, 'blue' => PureEnum::three]
PureEnum::groupBy('color'); // ['red' => [PureEnum::one], 'green' => [PureEnum::two], 'blue' => [PureEnum::three]]
PureEnum::names(); // ['one', 'two', 'three']
PureEnum::values(); // []
PureEnum::pluck(); // ['one', 'two', 'three']
PureEnum::pluck('color'); // ['red', 'green', 'blue']
PureEnum::pluck(fn (PureEnum $case) => $case->isOdd()); // [true, false, true]
PureEnum::pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
PureEnum::pluck(fn (PureEnum $case) => $case->isOdd(), fn (PureEnum $case) => $case->name); // ['one' => true, 'two' => false, 'three' => true]
PureEnum::filter('isOdd'); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::filter(fn (PureEnum $case) => $case->isOdd()); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::only('two', 'three'); // CasesCollection<PureEnum::two, PureEnum::three>
PureEnum::except('two', 'three'); // CasesCollection<PureEnum::one>
PureEnum::onlyValues(2, 3); // CasesCollection<>
PureEnum::exceptValues(2, 3); // CasesCollection<>
PureEnum::sort(); // CasesCollection<PureEnum::one, PureEnum::three, PureEnum::two>
PureEnum::sortDesc(); // CasesCollection<PureEnum::two, PureEnum::three, PureEnum::one>
PureEnum::sortByValue(); // CasesCollection<>
PureEnum::sortDescByValue(); // CasesCollection<>
PureEnum::sortBy('color'); // CasesCollection<PureEnum::three, PureEnum::two, PureEnum::one>
PureEnum::sortDescBy(fn (PureEnum $case) => $case->color()); // CasesCollection<PureEnum::one, PureEnum::two, PureEnum::three>

BackedEnum::collect(); // CasesCollection<BackedEnum::one, BackedEnum::two, BackedEnum::three>
BackedEnum::count(); // 3
BackedEnum::casesByName(); // ['one' => BackedEnum::one, 'two' => BackedEnum::two, 'three' => BackedEnum::three]
BackedEnum::casesByValue(); // [1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]
BackedEnum::casesBy('color'); // ['red' => BackedEnum::one, 'green' => BackedEnum::two, 'blue' => BackedEnum::three]
BackedEnum::groupBy('color'); // ['red' => [BackedEnum::one], 'green' => [BackedEnum::two], 'blue' => [BackedEnum::three]]
BackedEnum::names(); // ['one', 'two', 'three']
BackedEnum::values(); // [1, 2, 3]
BackedEnum::pluck(); // [1, 2, 3]
BackedEnum::pluck('color'); // ['red', 'green', 'blue']
BackedEnum::pluck(fn (BackedEnum $case) => $case->isOdd()); // [true, false, true]
BackedEnum::pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
BackedEnum::pluck(fn (BackedEnum $case) => $case->isOdd(), fn (BackedEnum $case) => $case->name); // ['one' => true, 
BackedEnum::filter('isOdd'); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::filter(fn (BackedEnum $case) => $case->isOdd()); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::only('two', 'three'); // CasesCollection<BackedEnum::two, BackedEnum::three>
BackedEnum::except('two', 'three'); // CasesCollection<BackedEnum::one>
BackedEnum::onlyValues(2, 3); // CasesCollection<>
BackedEnum::exceptValues(2, 3); // CasesCollection<>'two' => false, 'three' => true]
BackedEnum::sort(); // CasesCollection<BackedEnum::one, BackedEnum::three, BackedEnum::two>
BackedEnum::sortDesc(); // CasesCollection<BackedEnum::two, BackedEnum::three, BackedEnum::one>
BackedEnum::sortByValue(); // CasesCollection<BackedEnum::one, BackedEnum::two, BackedEnum::three>
BackedEnum::sortDescByValue(); // CasesCollection<BackedEnum::three, BackedEnum::two, BackedEnum::one>
BackedEnum::sortBy('color'); // CasesCollection<BackedEnum::three, BackedEnum::two, BackedEnum::one>
BackedEnum::sortDescBy(fn (BackedEnum $case) => $case->color()); // CasesCollection<BackedEnum::one, BackedEnum::two, BackedEnum::three>
```


### Cases collection

When a plain list of cases is returned by one of the [cases operations](#elaborating-cases), it gets wrapped into a `CasesCollection` which provides a fluent API to perform further operations on the set of cases:

```php
PureEnum::filter('isOdd')->sortBy('color')->pluck('color', 'name'); // ['three' => 'blue', 'one' => 'red']
```

Cases can be collected by calling `collect()` or any other [cases operation](#elaborating-cases) returning a `CasesCollection`:

```php
PureEnum::collect(); // CasesCollection<PureEnum::one, PureEnum::two, PureEnum::three>

BackedEnum::only('one', 'two'); // CasesCollection<BackedEnum::one, BackedEnum::two>
```

We can iterate cases collections within any loop:

```php
foreach (PureEnum::collect() as $case) {
    echo $case->name;
}
```

Obtaining the underlying plain list of cases is easy:

```php
PureEnum::collect()->cases(); // [PureEnum::one, PureEnum::two, PureEnum::three]
```

Sometimes we may need to extract only the first case of the collection:

```php
PureEnum::filter(fn (PureEnum $case) => !$case->isOdd())->first(); // PureEnum::two
```

For reference, here are all the operations available in `CasesCollection`:

```php
PureEnum::collect()->cases(); // [PureEnum::one, PureEnum::two, PureEnum::three]
PureEnum::collect()->count(); // 3
PureEnum::collect()->first(); // PureEnum::one
PureEnum::collect()->keyByName(); // ['one' => PureEnum::one, 'two' => PureEnum::two, 'three' => PureEnum::three]
PureEnum::collect()->keyByValue(); // []
PureEnum::collect()->keyBy('color'); // ['red' => PureEnum::one, 'green' => PureEnum::two, 'blue' => PureEnum::three]
PureEnum::collect()->groupBy('color'); // ['red' => [PureEnum::one], 'green' => [PureEnum::two], 'blue' => [PureEnum::three]]
PureEnum::collect()->names(); // ['one', 'two', 'three']
PureEnum::collect()->values(); // []
PureEnum::collect()->pluck(); // ['one', 'two', 'three']
PureEnum::collect()->pluck('color'); // ['red', 'green', 'blue']
PureEnum::collect()->pluck(fn (PureEnum $case) => $case->isOdd()); // [true, false, true]
PureEnum::collect()->pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
PureEnum::collect()->pluck(fn (PureEnum $case) => $case->isOdd(), fn (PureEnum $case) => $case->name); // ['one' => true, 'two' => false, 'three' => true]
PureEnum::collect()->filter('isOdd'); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::collect()->filter(fn (PureEnum $case) => $case->isOdd()); // CasesCollection<PureEnum::one, PureEnum::three>
PureEnum::collect()->only('two', 'three'); // CasesCollection<PureEnum::two, PureEnum::three>
PureEnum::collect()->except('two', 'three'); // CasesCollection<PureEnum::one>
PureEnum::collect()->onlyValues(2, 3); // CasesCollection<>
PureEnum::collect()->exceptValues(2, 3); // CasesCollection<>
PureEnum::collect()->sort(); // CasesCollection<PureEnum::one, PureEnum::three, PureEnum::two>
PureEnum::collect()->sortDesc(); // CasesCollection<PureEnum::two, PureEnum::three, PureEnum::one>
PureEnum::collect()->sortByValue(); // CasesCollection<>
PureEnum::collect()->sortDescByValue(); // CasesCollection<>
PureEnum::collect()->sortBy('color'); // CasesCollection<PureEnum::three, PureEnum::two, PureEnum::one>
PureEnum::collect()->sortDescBy(fn (PureEnum $case) => $case->color()); // CasesCollection<PureEnum::one, PureEnum::two, PureEnum::three>

BackedEnum::collect()->cases(); // [BackedEnum::one, BackedEnum::two, BackedEnum::three]
BackedEnum::collect()->count(); // 3
BackedEnum::collect()->first(); // BackedEnum::one
BackedEnum::collect()->keyByName(); // ['one' => BackedEnum::one, 'two' => BackedEnum::two, 'three' => BackedEnum::three]
BackedEnum::collect()->keyByValue(); // [1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]
BackedEnum::collect()->keyBy('color'); // ['red' => BackedEnum::one, 'green' => BackedEnum::two, 'blue' => BackedEnum::three]
BackedEnum::collect()->groupBy('color'); // ['red' => [BackedEnum::one], 'green' => [BackedEnum::two], 'blue' => [BackedEnum::three]]
BackedEnum::collect()->names(); // ['one', 'two', 'three']
BackedEnum::collect()->values(); // [1, 2, 3]
BackedEnum::collect()->pluck(); // [1, 2, 3]
BackedEnum::collect()->pluck('color'); // ['red', 'green', 'blue']
BackedEnum::collect()->pluck(fn (BackedEnum $case) => $case->isOdd()); // [true, false, true]
BackedEnum::collect()->pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
BackedEnum::collect()->pluck(fn (BackedEnum $case) => $case->isOdd(), fn (BackedEnum $case) => $case->name); // ['one' => true, 'two' => false, 'three' => true]
BackedEnum::collect()->filter('isOdd'); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::collect()->filter(fn (BackedEnum $case) => $case->isOdd()); // CasesCollection<BackedEnum::one, BackedEnum::three>
BackedEnum::collect()->only('two', 'three'); // CasesCollection<BackedEnum::two, BackedEnum::three>
BackedEnum::collect()->except('two', 'three'); // CasesCollection<BackedEnum::one>
BackedEnum::collect()->onlyValues(2, 3); // CasesCollection<BackedEnum::two, BackedEnum::three>
BackedEnum::collect()->exceptValues(2, 3); // CasesCollection<BackedEnum::one>
BackedEnum::collect()->sort(); // CasesCollection<BackedEnum::one, BackedEnum::three, BackedEnum::two>
BackedEnum::collect()->sortDesc(); // CasesCollection<BackedEnum::two, BackedEnum::three, BackedEnum::one>
BackedEnum::collect()->sortByValue(); // CasesCollection<BackedEnum::one, BackedEnum::two, BackedEnum::three>
BackedEnum::collect()->sortDescByValue(); // CasesCollection<BackedEnum::three, BackedEnum::two, BackedEnum::one>
BackedEnum::collect()->sortBy('color'); // CasesCollection<BackedEnum::three, BackedEnum::two, BackedEnum::one>
BackedEnum::collect()->sortDescBy(fn (BackedEnum $case) => $case->color()); // CasesCollection<BackedEnum::one, BackedEnum::two, BackedEnum::three>
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
[ico-actions]: https://img.shields.io/github/actions/workflow/status/cerbero90/enum/build.yml?branch=master&style=flat-square&logo=github
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-per]: https://img.shields.io/static/v1?label=compliance&message=PER&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer
[ico-phpstan]: https://img.shields.io/badge/level-max-success?style=flat-square&logo=data:image/png;base64,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
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/enum.svg?style=flat-square

[link-author]: https://twitter.com/cerbero90
[link-php]: https://www.php.net
[link-packagist]: https://packagist.org/packages/cerbero/enum
[link-actions]: https://github.com/cerbero90/enum/actions?query=workflow%3Abuild
[link-per]: https://www.php-fig.org/per/coding-style/
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/enum/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/enum
[link-downloads]: https://packagist.org/packages/cerbero/enum
[link-phpstan]: https://phpstan.org/
[link-contributors]: ../../contributors
