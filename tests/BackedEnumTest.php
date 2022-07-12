<?php

use Cerbero\Enum\CasesCollection;
use Cerbero\Enum\BackedEnum;

it('determines whether the enum is pure')
    ->expect(BackedEnum::isPure())
    ->toBeFalse();

it('determines whether the enum is backed')
    ->expect(BackedEnum::isBacked())
    ->toBeTrue();

it('retrieves all the names of the cases')
    ->expect(BackedEnum::names())
    ->toBe(['one', 'two', 'three']);

it('retrieves all the values of the backed cases')
    ->expect(BackedEnum::values())
    ->toBe([1, 2, 3]);

it('retrieves a collection with all the cases')
    ->expect(BackedEnum::collect())
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves all cases keyed by name')
    ->expect(BackedEnum::casesByName())
    ->toBe(['one' => BackedEnum::one, 'two' => BackedEnum::two, 'three' => BackedEnum::three]);

it('retrieves all cases keyed by value')
    ->expect(BackedEnum::casesByValue())
    ->toBe([1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]);

it('retrieves all cases keyed by a custom key')
    ->expect(BackedEnum::casesBy('color'))
    ->toBe(['red' => BackedEnum::one, 'green' => BackedEnum::two, 'blue' => BackedEnum::three]);

it('retrieves all cases keyed by the result of a closure')
    ->expect(BackedEnum::casesBy(fn (BackedEnum $case) => $case->shape()))
    ->toBe(['triangle' => BackedEnum::one, 'square' => BackedEnum::two, 'circle' => BackedEnum::three]);

it('retrieves all cases grouped by a custom key', function () {
    expect(BackedEnum::groupBy('color'))
        ->toBe(['red' => [BackedEnum::one], 'green' => [BackedEnum::two], 'blue' => [BackedEnum::three]]);
});

it('retrieves all cases grouped by the result of a closure', function () {
    expect(BackedEnum::groupBy(fn (BackedEnum $case) => $case->isOdd()))
        ->toBe([1 => [BackedEnum::one, BackedEnum::three], 0 => [BackedEnum::two]]);
});

it('retrieves a collection with the filtered cases')
    ->expect(BackedEnum::filter(fn (UnitEnum $case) => $case->name !== 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::two]);

it('retrieves a collection with cases filtered by a key', function () {
    expect(BackedEnum::filter('isOdd'))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([BackedEnum::one, BackedEnum::three]);
});

it('retrieves a collection of cases having the given names')
    ->expect(BackedEnum::only('two', 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of cases not having the given names')
    ->expect(BackedEnum::except('one', 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two]);

it('retrieves a collection of backed cases having the given values')
    ->expect(BackedEnum::onlyValues(2, 3))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of backed cases not having the given values')
    ->expect(BackedEnum::exceptValues(1, 3))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two]);

it('retrieves an array of values')
    ->expect(BackedEnum::pluck())
    ->toBe([1, 2, 3]);

it('retrieves an array of custom values')
    ->expect(BackedEnum::pluck('color'))
    ->toBe(['red', 'green', 'blue']);

it('retrieves an associative array with custom keys and values')
    ->expect(BackedEnum::pluck('color', 'shape'))
    ->toBe(['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']);

it('retrieves an associative array with keys and values resolved from closures')
    ->expect(BackedEnum::pluck(fn (BackedEnum $case) => $case->name, fn (BackedEnum $case) => $case->color()))
    ->toBe(['red' => 'one', 'green' => 'two', 'blue' => 'three']);

it('determines whether an enum has a target')
    ->expect(fn (mixed $target, bool $result) => BackedEnum::has($target) === $result)
    ->toBeTrue()
    ->with([
        [BackedEnum::one, true],
        [new stdClass(), false],
        ['one', false],
        ['four', false],
        [1, true],
        [4, false],
        ['1', false],
        ['4', false],
    ]);

it('determines whether an enum does not have a target')
    ->expect(fn (mixed $target, bool $result) => BackedEnum::doesntHave($target) === $result)
    ->toBeTrue()
    ->with([
        [BackedEnum::one, false],
        [new stdClass(), true],
        ['one', true],
        ['four', true],
        [1, false],
        [4, true],
        ['1', true],
        ['4', true],
    ]);

it('determines whether an enum case matches a target')
    ->expect(fn (mixed $target, bool $result) => BackedEnum::one->is($target) === $result)
    ->toBeTrue()
    ->with([
        [BackedEnum::one, true],
        [BackedEnum::two, false],
        ['one', false],
        ['two', false],
        [1, true],
        [2, false],
        ['1', false],
        ['2', false],
    ]);

it('determines whether an enum case does not match a target')
    ->expect(fn (mixed $target, bool $result) => BackedEnum::one->isNot($target) === $result)
    ->toBeTrue()
    ->with([
        [BackedEnum::one, false],
        [BackedEnum::two, true],
        ['one', true],
        ['two', true],
        [1, false],
        [2, true],
        ['1', true],
        ['2', true],
    ]);

it('determines whether an enum case matches some targets')
    ->expect(fn (mixed $targets, bool $result) => BackedEnum::one->in($targets) === $result)
    ->toBeTrue()
    ->with([
        [[BackedEnum::one, BackedEnum::two], true],
        [[BackedEnum::two, BackedEnum::three], false],
        [['one', 'two'], false],
        [['two', 'three'], false],
        [[1, 2], true],
        [[2, 3], false],
        [['1', '2'], false],
        [['2', '3'], false],
    ]);

it('determines whether an enum case does not match any target')
    ->expect(fn (mixed $targets, bool $result) => BackedEnum::one->notIn($targets) === $result)
    ->toBeTrue()
    ->with([
        [[BackedEnum::one, BackedEnum::two], false],
        [[BackedEnum::two, BackedEnum::three], true],
        [['one', 'two'], true],
        [['two', 'three'], true],
        [[1, 2], false],
        [[2, 3], true],
        [['1', '2'], true],
        [['2', '3'], true],
    ]);

it('retrieves a collection of cases sorted by name ascending')
    ->expect(BackedEnum::sort())
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::three, BackedEnum::two]);

it('retrieves a collection of cases sorted by name descending')
    ->expect(BackedEnum::sortDesc())
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two, BackedEnum::three, BackedEnum::one]);

it('retrieves a collection of cases sorted by value ascending')
    ->expect(BackedEnum::sortByValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of cases sorted by value descending')
    ->expect(BackedEnum::sortDescByValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::three, BackedEnum::two, BackedEnum::one]);

it('retrieves a collection of cases sorted by a custom value ascending')
    ->expect(BackedEnum::sortBy('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::three, BackedEnum::two, BackedEnum::one]);

it('retrieves a collection of cases sorted by a custom value descending')
    ->expect(BackedEnum::sortDescBy('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of cases sorted by the result of a closure ascending')
    ->expect(BackedEnum::sortBy(fn (BackedEnum $case) => $case->shape()))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::three, BackedEnum::two, BackedEnum::one]);

it('retrieves a collection of cases sorted by the result of a closure descending')
    ->expect(BackedEnum::sortDescBy(fn (BackedEnum $case) => $case->shape()))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves the count of cases')
    ->expect(BackedEnum::count())
    ->toBe(3);

it('retrieves the case hydrated from a value')
    ->expect(fn (int $value, BackedEnum $case) => BackedEnum::from($value) === $case)
    ->toBeTrue()
    ->with([
        [1, BackedEnum::one],
        [2, BackedEnum::two],
        [3, BackedEnum::three],
    ]);

it('throws a value error when hydrating backed cases with a missing value', fn () => BackedEnum::from(4))
    ->throws(ValueError::class, '4 is not a valid backing value for enum "Cerbero\Enum\BackedEnum"');

it('retrieves the case hydrated from a value or returns null')
    ->expect(fn (int $value, ?BackedEnum $case) => BackedEnum::tryFrom($value) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        [1, BackedEnum::one],
        [2, BackedEnum::two],
        [3, BackedEnum::three],
        [4, null],
    ]);

it('retrieves the case hydrated from a name')
    ->expect(fn (string $name, BackedEnum $case) => BackedEnum::fromName($name) === $case)
    ->toBeTrue()
    ->with([
        ['one', BackedEnum::one],
        ['two', BackedEnum::two],
        ['three', BackedEnum::three],
    ]);

it('throws a value error when hydrating backed cases with a missing name', fn () => BackedEnum::fromName('four'))
    ->throws(ValueError::class, '"four" is not a valid name for enum "Cerbero\Enum\BackedEnum"');

it('retrieves the case hydrated from a name or returns null')
    ->expect(fn (string $name, ?BackedEnum $case) => BackedEnum::tryFromName($name) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['one', BackedEnum::one],
        ['two', BackedEnum::two],
        ['three', BackedEnum::three],
        ['four', null],
    ]);

it('retrieves the cases hydrated from a key')
    ->expect(fn (string $key, mixed $value, array $cases) => BackedEnum::fromKey($key, $value)->cases() === $cases)
    ->toBeTrue()
    ->with([
        ['color', 'red', [BackedEnum::one]],
        ['name', 'three', [BackedEnum::three]],
        ['isOdd', true, [BackedEnum::one, BackedEnum::three]],
    ]);

it('retrieves the cases hydrated from a key using a closure')
    ->expect(BackedEnum::fromKey(fn (BackedEnum $case) => $case->shape(), 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two]);

it('throws a value error when hydrating cases with an invalid key', fn () => BackedEnum::fromKey('color', 'orange'))
    ->throws(ValueError::class, 'Invalid value for the key "color" for enum "Cerbero\Enum\BackedEnum"');

it('retrieves the case hydrated from a key or returns null')
    ->expect(fn (string $key, mixed $value, ?array $cases) => BackedEnum::tryFromKey($key, $value)?->cases() === $cases)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['color', 'red', [BackedEnum::one]],
        ['name', 'three', [BackedEnum::three]],
        ['isOdd', true, [BackedEnum::one, BackedEnum::three]],
        ['shape', 'rectangle', null],
    ]);

it('attempts to retrieve the case hydrated from a key using a closure')
    ->expect(BackedEnum::tryFromKey(fn (BackedEnum $case) => $case->shape(), 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->cases()
    ->toBe([BackedEnum::two]);

it('retrieves the key of a case')
    ->expect(fn (string $key, mixed $value) => BackedEnum::one->get($key) === $value)
    ->toBeTrue()
    ->with([
        ['name', 'one'],
        ['value', 1],
        ['color', 'red'],
        ['shape', 'triangle'],
    ]);

it('retrieves the key of a case using a closure')
    ->expect(BackedEnum::one->get(fn (BackedEnum $case) => $case->color()))
    ->toBe('red');

it('throws a value error when attempting to retrieve an invalid key', fn () => BackedEnum::one->get('invalid'))
    ->throws(ValueError::class, '"invalid" is not a valid key for enum "Cerbero\Enum\BackedEnum"');
