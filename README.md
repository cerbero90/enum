# 🎲 Enum

[![Author][ico-author]][link-author]
[![PHP Version][ico-php]][link-php]
[![Build Status][ico-actions]][link-actions]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![PHPStan Level][ico-phpstan]][link-phpstan]
[![Total Downloads][ico-downloads]][link-downloads]
[![Latest Version][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![PER][ico-per]][link-per]

Zero-dependencies package to supercharge enum functionalities.

> [!TIP]
> Need to supercharge enums in a Laravel application?
>
> Consider using [🎲 Laravel Enum](https://github.com/cerbero90/laravel-enum) instead.


## 📦 Install

Via Composer:

``` bash
composer require cerbero/enum
```

## 🔮 Usage

* [⚖️ Comparison](#%EF%B8%8F-comparison)
* [🏷️ Meta](#%EF%B8%8F-meta)
* [🚰 Hydration](#-hydration)
* [🎲 Enum operations](#-enum-operations)
* [🧺 Cases collection](#-cases-collection)
* [🪄 Magic](#-magic)
* [🤳 Self-awareness](#-self-awareness)
* [🦾 Console commands](#-console-commands)
  * [🗒️ annotate](#%EF%B8%8F-annotate)
  * [🏗️ make](#%EF%B8%8F-make)

To supercharge our enums with all the features provided by this package, we can let our enums use the `Enumerates` trait:

```php
use Cerbero\Enum\Concerns\Enumerates;

enum PureEnum
{
    use Enumerates;

    case One;
    case Two;
    case Three;
}

enum BackedEnum: int
{
    use Enumerates;

    case One = 1;
    case Two = 2;
    case Three = 3;
}
```


### ⚖️ Comparison

We can check whether an enum includes some names or values. Pure enums check for names and backed enums check for values:

```php
PureEnum::has('One'); // true
PureEnum::has('four'); // false
PureEnum::doesntHave('One'); // false
PureEnum::doesntHave('four'); // true

BackedEnum::has(1); // true
BackedEnum::has(4); // false
BackedEnum::doesntHave(1); // false
BackedEnum::doesntHave(4); // true
```

Otherwise we can check whether cases match a given name or value:

```php
PureEnum::One->is('One'); // true
PureEnum::One->is(1); // false
PureEnum::One->is('four'); // false
PureEnum::One->isNot('One'); // false
PureEnum::One->isNot(1); // true
PureEnum::One->isNot('four'); // true

BackedEnum::One->is(1); // true
BackedEnum::One->is('1'); // false
BackedEnum::One->is(4); // false
BackedEnum::One->isNot(1); // false
BackedEnum::One->isNot('1'); // true
BackedEnum::One->isNot(4); // true
```

Comparisons can also be performed against arrays:

```php
PureEnum::One->in(['One', 'four']); // true
PureEnum::One->in([1, 4]); // false
PureEnum::One->notIn(['One', 'four']); // false
PureEnum::One->notIn([1, 4]); // true

BackedEnum::One->in([1, 4]); // true
BackedEnum::One->in(['One', 'four']); // false
BackedEnum::One->notIn([1, 4]); // false
BackedEnum::One->notIn(['One', 'four']); // true
```


### 🏷️ Meta

Meta add extra information to a case. Meta can be added by implementing a public non-static method and/or by attaching `#[Meta]` attributes to cases:

```php
enum BackedEnum: int
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case One = 1;

    #[Meta(color: 'green', shape: 'square')]
    case Two = 2;

    #[Meta(color: 'blue', shape: 'circle')]
    case Three = 3;

    public function isOdd(): bool
    {
        return $this->value % 2 != 0;
    }
}
```

The above enum defines 3 meta for each case: `color`, `shape` and `isOdd`. The `#[Meta]` attributes are ideal to declare static information, whilst public non-static methods are ideal to declare dynamic information.

To access a case meta, we can simply call the method having the same name of the wanted meta:

```php
BackedEnum::Two->color(); // green
```

> [!TIP]
> Our IDE can autocomplete meta methods thanks to the [`annotate` command](#-annotate).

`#[Meta]` attributes can also be attached to the enum itself to provide default values when a case does not declare its own meta values:

```php
#[Meta(color: 'red', shape: 'triangle')]
enum BackedEnum: int
{
    use Enumerates;

    case One = 1;

    #[Meta(color: 'green', shape: 'square')]
    case Two = 2;

    case Three = 3;
}
```

In the above example all cases have a `red` color and a `triangle` shape, except the case `Two` that overrides the default meta values.

Meta can also be leveraged for the [hydration](#-hydration), [elaboration](#-enum-operations) and [collection](#-cases-collection) of cases.


### 🚰 Hydration

An enum case can be instantiated from its own name, value (if backed) or [meta](#%EF%B8%8F-meta):

```php
PureEnum::from('One'); // PureEnum::One
PureEnum::from('four'); // throws ValueError
PureEnum::tryFrom('One'); // PureEnum::One
PureEnum::tryFrom('four'); // null
PureEnum::fromName('One'); // PureEnum::One
PureEnum::fromName('four'); // throws ValueError
PureEnum::tryFromName('One'); // PureEnum::One
PureEnum::tryFromName('four'); // null
PureEnum::fromMeta('color', 'red'); // CasesCollection[PureEnum::One]
PureEnum::fromMeta('color', 'purple'); // throws ValueError
PureEnum::fromMeta('isOdd'); // CasesCollection[PureEnum::One, PureEnum::Three]
PureEnum::fromMeta('shape', fn(string $shape) => in_array($shape, ['square', 'circle'])); // CasesCollection[PureEnum::One, PureEnum::Three]
PureEnum::tryFromMeta('color', 'red'); // CasesCollection[PureEnum::One]
PureEnum::tryFromMeta('color', 'purple'); // null
PureEnum::tryFromMeta('isOdd'); // CasesCollection[PureEnum::One, PureEnum::Three]
PureEnum::tryFromMeta('shape', fn(string $shape) => in_array($shape, ['square', 'circle'])); // CasesCollection[PureEnum::One, PureEnum::Three]

BackedEnum::from(1); // BackedEnum::One
BackedEnum::from('1'); // throws ValueError
BackedEnum::tryFrom(1); // BackedEnum::One
BackedEnum::tryFrom('1'); // null
BackedEnum::fromName('One'); // BackedEnum::One
BackedEnum::fromName('four'); // throws ValueError
BackedEnum::tryFromName('One'); // BackedEnum::One
BackedEnum::tryFromName('four'); // null
BackedEnum::fromMeta('color', 'red'); // CasesCollection[BackedEnum::One]
BackedEnum::fromMeta('color', 'purple'); // throws ValueError
BackedEnum::fromMeta('isOdd'); // CasesCollection[PureEnum::One, PureEnum::Three]
BackedEnum::fromMeta('shape', fn(string $shape) => in_array($shape, ['square', 'circle'])); // CasesCollection[BackedEnum::One, BackedEnum::Three]
BackedEnum::tryFromMeta('color', 'red'); // CasesCollection[BackedEnum::One]
BackedEnum::tryFromMeta('color', 'purple'); // null
BackedEnum::tryFromMeta('isOdd'); // CasesCollection[PureEnum::One, PureEnum::Three]
BackedEnum::tryFromMeta('shape', fn(string $shape) => in_array($shape, ['square', 'circle'])); // CasesCollection[BackedEnum::One, BackedEnum::Three]
```

Hydrating from meta can return multiple cases. To facilitate further processing, such cases are [collected into a `CasesCollection`](#-cases-collection).


### 🎲 Enum operations

A number of operations can be performed against an enum to affect all its cases:

```php
PureEnum::collect(); // CasesCollection[PureEnum::One, PureEnum::Two, PureEnum::Three]
PureEnum::count(); // 3
PureEnum::first(); // PureEnum::One
PureEnum::first(fn(PureEnum $case,  int $key) => ! $case->isOdd()); // PureEnum::Two
PureEnum::names(); // ['One', 'Two', 'Three']
PureEnum::values(); // []
PureEnum::pluck('name'); // ['One', 'Two', 'Three']
PureEnum::pluck('color'); // ['red', 'green', 'blue']
PureEnum::pluck(fn(PureEnum $case) => $case->isOdd()); // [true, false, true]
PureEnum::pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
PureEnum::pluck(fn(PureEnum $case) => $case->isOdd(), fn(PureEnum $case) => $case->name); // ['One' => true, 'Two' => false, 'Three' => true]
PureEnum::map(fn(PureEnum $case, int $key) => $case->name . $key); // ['One0', 'Two1', 'Three2']
PureEnum::keyByName(); // CasesCollection['One' => PureEnum::One, 'Two' => PureEnum::Two, 'Three' => PureEnum::Three]
PureEnum::keyBy('color'); // CasesCollection['red' => PureEnum::One, 'green' => PureEnum::Two, 'blue' => PureEnum::Three]
PureEnum::keyByValue(); // CasesCollection[]
PureEnum::groupBy('color'); // ['red' => CasesCollection[PureEnum::One], 'green' => CasesCollection[PureEnum::Two], 'blue' => CasesCollection[PureEnum::Three]]
PureEnum::filter('isOdd'); // CasesCollection[PureEnum::One, PureEnum::Three]
PureEnum::filter(fn(PureEnum $case) => $case->isOdd()); // CasesCollection[PureEnum::One, PureEnum::Three]
PureEnum::only('Two', 'Three'); // CasesCollection[PureEnum::Two, PureEnum::Three]
PureEnum::except('Two', 'Three'); // CasesCollection[PureEnum::One]
PureEnum::onlyValues(2, 3); // CasesCollection[]
PureEnum::exceptValues(2, 3); // CasesCollection[]
PureEnum::sort(); // CasesCollection[PureEnum::One, PureEnum::Three, PureEnum::Two]
PureEnum::sortBy('color'); // CasesCollection[PureEnum::Three, PureEnum::Two, PureEnum::One]
PureEnum::sortByValue(); // CasesCollection[]
PureEnum::sortDesc(); // CasesCollection[PureEnum::Two, PureEnum::Three, PureEnum::One]
PureEnum::sortByDesc(fn(PureEnum $case) => $case->color()); // CasesCollection[PureEnum::One, PureEnum::Two, PureEnum::Three]
PureEnum::sortByDescValue(); // CasesCollection[]

BackedEnum::collect(); // CasesCollection[BackedEnum::One, BackedEnum::Two, BackedEnum::Three]
BackedEnum::count(); // 3
BackedEnum::first(); // BackedEnum::One
BackedEnum::first(fn(BackedEnum $case,  int $key) => ! $case->isOdd()); // BackedEnum::Two
BackedEnum::names(); // ['One', 'Two', 'Three']
BackedEnum::values(); // [1, 2, 3]
BackedEnum::pluck('value'); // [1, 2, 3]
BackedEnum::pluck('color'); // ['red', 'green', 'blue']
BackedEnum::pluck(fn(BackedEnum $case) => $case->isOdd()); // [true, false, true]
BackedEnum::pluck('color', 'shape'); // ['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']
BackedEnum::pluck(fn(BackedEnum $case) => $case->isOdd(), fn(BackedEnum $case) => $case->name); // ['One' => true, 'Two' => false, 'Three' => true]
BackedEnum::map(fn(BackedEnum $case, int $key) => $case->name . $key); // ['One0', 'Two1', 'Three2']
BackedEnum::keyByName(); // CasesCollection['One' => BackedEnum::One, 'Two' => BackedEnum::Two, 'Three' => BackedEnum::Three]
BackedEnum::keyBy('color'); // CasesCollection['red' => BackedEnum::One, 'green' => BackedEnum::Two, 'blue' => BackedEnum::Three]
BackedEnum::keyByValue(); // CasesCollection[1 => BackedEnum::One, 2 => BackedEnum::Two, 3 => BackedEnum::Three]
BackedEnum::groupBy('color'); // ['red' => CasesCollection[BackedEnum::One], 'green' => CasesCollection[BackedEnum::Two], 'blue' => CasesCollection[BackedEnum::Three]]
BackedEnum::filter('isOdd'); // CasesCollection[BackedEnum::One, BackedEnum::Three]
BackedEnum::filter(fn(BackedEnum $case) => $case->isOdd()); // CasesCollection[BackedEnum::One, BackedEnum::Three]
BackedEnum::only('Two', 'Three'); // CasesCollection[BackedEnum::Two, BackedEnum::Three]
BackedEnum::except('Two', 'Three'); // CasesCollection[BackedEnum::One]
BackedEnum::onlyValues(2, 3); // CasesCollection[]
BackedEnum::exceptValues(2, 3); // CasesCollection['Two' => false, 'Three' => true]
BackedEnum::sort(); // CasesCollection[BackedEnum::One, BackedEnum::Three, BackedEnum::Two]
BackedEnum::sortBy('color'); // CasesCollection[BackedEnum::Three, BackedEnum::Two, BackedEnum::One]
BackedEnum::sortByValue(); // CasesCollection[BackedEnum::One, BackedEnum::Two, BackedEnum::Three]
BackedEnum::sortDesc(); // CasesCollection[BackedEnum::Two, BackedEnum::Three, BackedEnum::One]
BackedEnum::sortByDescValue(); // CasesCollection[BackedEnum::Three, BackedEnum::Two, BackedEnum::One]
BackedEnum::sortByDesc(fn(BackedEnum $case) => $case->color()); // CasesCollection[BackedEnum::One, BackedEnum::Two, BackedEnum::Three]
```


### 🧺 Cases collection

When an [enum operation](#-enum-operations) can return multiple cases, they are collected into a `CasesCollection` which provides a fluent API to perform further operations on the set of cases:

```php
PureEnum::filter('isOdd')->sortBy('color')->pluck('color', 'name'); // ['Three' => 'blue', 'One' => 'red']
```

Cases can be collected by calling `collect()` or any other [enum operation](#-enum-operations) returning a `CasesCollection`:

```php
PureEnum::collect(); // CasesCollection[PureEnum::One, PureEnum::Two, PureEnum::Three]

BackedEnum::only('One', 'Two'); // CasesCollection[BackedEnum::One, BackedEnum::Two]
```

We can iterate a cases collection within any loop:

```php
foreach (PureEnum::collect() as $case) {
    echo $case->name;
}
```

All the [enum operations listed above](#-enum-operations) are also available when dealing with a collection of cases.


### 🪄 Magic

Enums can implement magic methods to be invoked or to handle calls to inaccessible methods. By default when calling an inaccessible static method, the name or value of the case matching the missing method is returned:

```php
PureEnum::One(); // 'One'

BackedEnum::One(); // 1
```

> [!TIP]
> Our IDE can autocomplete cases static methods thanks to the [`annotate` command](#-annotate).

We can also obtain the name or value of a case by simply invoking it:

```php
$case = PureEnum::One;
$case(); // 'One'

$case = BackedEnum::One;
$case(); // 1
```

When calling an inaccessible method of a case, by default the value of the meta matching the missing method is returned:

```php
PureEnum::One->color(); // 'red'

BackedEnum::One->shape(); // 'triangle'
```

> [!TIP]
> Our IDE can autocomplete meta methods thanks to the [`annotate` command](#-annotate).

Depending on our needs, we can customize the default behavior of all enums in our application when invoking a case or calling inaccessible methods:

```php
use Cerbero\Enum\Enums;
use UnitEnum;

// define the logic to run when calling an inaccessible method of an enum
Enums::onStaticCall(function(string $enum, string $name, array $arguments) {
    // $enum is the fully qualified name of the enum that called the inaccessible method
    // $name is the inaccessible method name
    // $arguments are the parameters passed to the inaccessible method
});

// define the logic to run when calling an inaccessible method of a case
Enums::onCall(function(UnitEnum $case, string $name, array $arguments) {
    // $case is the instance of the case that called the inaccessible method
    // $name is the inaccessible method name
    // $arguments are the parameters passed to the inaccessible method
});

// define the logic to run when invoking a case
Enums::onInvoke(function(UnitEnum $case, mixed ...$arguments) {
    // $case is the instance of the case that is being invoked
    // $arguments are the parameters passed when invoking the case
});
```


### 🤳 Self-awareness

Some internal methods are also available and can be useful for inspecting enums or auto-generating code:

```php
PureEnum::isPure(); // true
PureEnum::isBacked(); // false
PureEnum::isBackedByInteger(); // false
PureEnum::isBackedByString(); // false
PureEnum::metaNames(); // ['color', 'shape', 'isOdd']
PureEnum::metaAttributeNames(); // ['color', 'shape']
PureEnum::One->resolveItem('name'); // 'One'
PureEnum::One->resolveMeta('isOdd'); // true
PureEnum::One->resolveMetaAttribute('color'); // 'red'
PureEnum::One->value(); // 'One'

BackedEnum::isPure(); // false
BackedEnum::isBacked(); // true
BackedEnum::isBackedByInteger(); // true
BackedEnum::isBackedByString(); // false
BackedEnum::metaNames(); // ['color', 'shape', 'isOdd']
BackedEnum::metaAttributeNames(); // ['color', 'shape']
BackedEnum::One->resolveItem('value'); // 1
BackedEnum::One->resolveMeta('isOdd'); // true
BackedEnum::One->resolveMetaAttribute('color'); // 'red'
BackedEnum::One->value(); // 1
```


### 🦾 Console commands

This package provides a handy binary, built to automate different tasks. To learn how to use it, we can simply run it:

```bash
./vendor/bin/enum
```

#### 🗒️ annotate

The `annotate` command automatically adds method annotations to enums, making IDEs autocompletion possible:

```bash
./vendor/bin/enum annotate App/Enums/Enum

./vendor/bin/enum annotate "App\Enums\Enum"
```

We can provide more than one enum to annotate, if needed:

```bash
./vendor/bin/enum annotate App/Enums/Enum1 App/Enums/Enum2

./vendor/bin/enum annotate "App\Enums\Enum1" "App\Enums\Enum2"
```

Otherwise we can annotate all our enums at once by enabling the option `--all`:

```bash
./vendor/bin/enum annotate --all

./vendor/bin/enum annotate -a
```

For the option `--all` to work, we need to set the paths where enums live in our application:

```php
use Cerbero\Enum\Enums;

Enums::setPaths('app/Enums', 'domain/*/Enums');
```

In the above example, enums are discovered in the `app/Enums` directory and in all `Enums` sub-folders belonging to `domain`, e.g. `domain/Posts/Enums`, `domain/Users/Enums`, etc.

This package tries to automatically find the application base path. However if enums can't be discovered after setting their paths, we can manually set our application base path:

```php
Enums::setBasePath(__DIR__ . '/path/to/our/app');
```

If we want to overwrite method annotations already annotated on enums, we can add the option `--force`:

```bash
php artisan enum:annotate App/Enums/Enum --force

php artisan enum:annotate App/Enums/Enum -f
```

#### 🏗️ make

The `make` command creates a new - automatically annotated - enum with the cases that we provide:

```bash
./vendor/bin/enum make App/Enums/Enum CaseOne CaseTwo

./vendor/bin/enum make "App\Enums\Enum" CaseOne CaseTwo
```

If we need to create backed enums, we can specify a custom value for each case:

```bash
./vendor/bin/enum make App/Enums/Enum CaseOne=value1 CaseTwo=value2
```

Otherwise we can automatically assign values to cases by setting the `--backed` option:

```bash
./vendor/bin/enum make App/Enums/Enum CaseOne CaseTwo --backed=int0
```

The option `--backed` supports the following values:

- `int0`: assign an incremental integer starting from 0 (0, 1, 2...)
- `int1`: assign an incremental integer starting from 1 (1, 2, 3...)
- `bitwise`: assign an incremental bitwise value (1, 2, 4...)
- `snake`: assign the case name in snake case (case_one, case_two...)
- `kebab`: assign the case name in kebab case (case-one, case-two...)
- `camel`: assign the case name in camel case (caseOne, caseTwo...)
- `lower`: assign the case name in lower case (caseone, casetwo...)
- `upper`: assign the case name in upper case (CASEONE, CASETWO...)

If we want to overwrite an existing enum, we can add the option `--force`:

```bash
php artisan enum:make App/Enums/Enum CaseOne CaseTwo --force

php artisan enum:make App/Enums/Enum CaseOne CaseTwo -f
```

This package tries to automatically find the application base path. However if enums can't be successfully created, we can manually set our application base path:

```php
Enums::setBasePath(__DIR__ . '/path/to/our/app');
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

[ico-author]: https://img.shields.io/badge/author-cerbero90-blue?logo=x&style=flat-square&logoSize=auto
[ico-php]: https://img.shields.io/packagist/php-v/cerbero/enum?color=%23767bb5&logo=php&style=flat-square&logoSize=auto
[ico-version]: https://img.shields.io/packagist/v/cerbero/enum.svg?label=version&style=flat-square
[ico-actions]: https://img.shields.io/github/actions/workflow/status/cerbero90/enum/build.yml?branch=master&style=flat-square&logo=github&logoSize=auto
[ico-license]: https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square
[ico-per]: https://img.shields.io/static/v1?label=compliance&message=PER&color=blue&style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer&logoSize=auto
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cerbero90/enum.svg?style=flat-square&logo=scrutinizer&logoSize=auto
[ico-phpstan]: https://img.shields.io/badge/level-max-success?style=flat-square&logo=data:image/png;base64,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&logoSize=auto
[ico-downloads]: https://img.shields.io/packagist/dt/cerbero/enum.svg?style=flat-square

[link-author]: https://x.com/cerbero90
[link-php]: https://www.php.net
[link-packagist]: https://packagist.org/packages/cerbero/enum
[link-actions]: https://github.com/cerbero90/enum/actions?query=workflow%3Abuild
[link-per]: https://www.php-fig.org/per/coding-style/
[link-scrutinizer]: https://scrutinizer-ci.com/g/cerbero90/enum/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cerbero90/enum
[link-downloads]: https://packagist.org/packages/cerbero/enum
[link-phpstan]: https://phpstan.org/
[link-contributors]: ../../contributors
