<?php

use Cerbero\Enum\CasesCollection;
use Cerbero\Enum\BackedEnum;
use Cerbero\Enum\Enums;
use Cerbero\Enum\PureEnum;
use Pest\Expectation;

it('determines whether the enum is pure')
    ->expect(BackedEnum::isPure())
    ->toBeFalse();

it('determines whether the enum is backed')
    ->expect(BackedEnum::isBacked())
    ->toBeTrue();

it('determines whether the enum is backed by integer or string', function() {
    expect(BackedEnum::isBackedByInteger())->toBeTrue();
    expect(BackedEnum::isBackedByString())->toBeFalse();
    expect(PureEnum::isBackedByInteger())->toBeFalse();
    expect(PureEnum::isBackedByString())->toBeFalse();
});

it('retrieves all the names of the cases')
    ->expect(BackedEnum::names())
    ->toBe(['one', 'two', 'three']);

it('retrieves the first case', fn() => expect(BackedEnum::first())->toBe(BackedEnum::one));

it('retrieves the first case with a closure')
    ->expect(BackedEnum::first(fn(BackedEnum $case) => !$case->isOdd()))
    ->toBe(BackedEnum::two);

it('retrieves the result of mapping over all the cases', function() {
    $cases = $keys = [];

    $mapped = BackedEnum::map(function(BackedEnum $case, int $key) use (&$cases, &$keys) {
        $cases[] = $case;
        $keys[] = $key;

        return $case->color();
    });

    expect($cases)->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three])
        ->and($keys)->toBe([0, 1, 2])
        ->and($mapped)->toBe(['red', 'green', 'blue']);
});

it('retrieves all the values of the backed cases')
    ->expect(BackedEnum::values())
    ->toBe([1, 2, 3]);

it('retrieves a collection with all the cases')
    ->expect(BackedEnum::collect())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves all cases keyed by name')
    ->expect(BackedEnum::keyByName())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe(['one' => BackedEnum::one, 'two' => BackedEnum::two, 'three' => BackedEnum::three]);

it('retrieves all cases keyed by value')
    ->expect(BackedEnum::keyByValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]);

it('retrieves all cases keyed by a custom key')
    ->expect(BackedEnum::keyBy('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe(['red' => BackedEnum::one, 'green' => BackedEnum::two, 'blue' => BackedEnum::three]);

it('retrieves all cases keyed by the result of a closure')
    ->expect(BackedEnum::keyBy(fn(BackedEnum $case) => $case->shape()))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $case, Expectation $key) => $key->toBe('triangle')->and($case)->toBe(BackedEnum::one),
            fn(Expectation $case, Expectation $key) => $key->toBe('square')->and($case)->toBe(BackedEnum::two),
            fn(Expectation $case, Expectation $key) => $key->toBe('circle')->and($case)->toBe(BackedEnum::three),
        );

it('retrieves all cases grouped by a custom key', function () {
    expect(BackedEnum::groupBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $cases, Expectation $key) => $key->toBe('red')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([BackedEnum::one]),
            fn(Expectation $cases, Expectation $key) => $key->toBe('green')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([BackedEnum::two]),
            fn(Expectation $cases, Expectation $key) => $key->toBe('blue')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([BackedEnum::three]),
        );
});

it('retrieves all cases grouped by the result of a closure', function () {
    expect(BackedEnum::groupBy(fn(BackedEnum $case) => $case->isOdd()))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([BackedEnum::one, BackedEnum::three]),
            fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([BackedEnum::two]),
        );
});

it('retrieves a collection with the filtered cases')
    ->expect(BackedEnum::filter(fn(UnitEnum $case) => $case->name !== 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::one, BackedEnum::two]);

it('retrieves a collection with cases filtered by a meta', function () {
    expect(BackedEnum::filter('isOdd'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => BackedEnum::one, 2 => BackedEnum::three]);
});

it('retrieves a collection of cases having the given names')
    ->expect(BackedEnum::only('two', 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::two, 2 => BackedEnum::three]);

it('retrieves a collection of cases not having the given names')
    ->expect(BackedEnum::except('one', 'three'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::two]);

it('retrieves a collection of backed cases having the given values')
    ->expect(BackedEnum::onlyValues(2, 3))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::two, 2 => BackedEnum::three]);

it('retrieves a collection of backed cases not having the given values')
    ->expect(BackedEnum::exceptValues(1, 3))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::two]);

it('retrieves an array of values')
    ->expect(BackedEnum::pluck('value'))
    ->toBe([1, 2, 3]);

it('retrieves an array of custom values')
    ->expect(BackedEnum::pluck('color'))
    ->toBe(['red', 'green', 'blue']);

it('retrieves an associative array with custom keys and values')
    ->expect(BackedEnum::pluck('color', 'shape'))
    ->toBe(['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']);

it('retrieves an associative array with keys and values resolved from closures')
    ->expect(BackedEnum::pluck(fn(BackedEnum $case) => $case->name, fn(BackedEnum $case) => $case->color()))
    ->toBe(['red' => 'one', 'green' => 'two', 'blue' => 'three']);

it('determines whether an enum has a target')
    ->expect(fn(mixed $target, bool $result) => BackedEnum::has($target) === $result)
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
    ->expect(fn(mixed $target, bool $result) => BackedEnum::doesntHave($target) === $result)
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
    ->expect(fn(mixed $target, bool $result) => BackedEnum::one->is($target) === $result)
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
    ->expect(fn(mixed $target, bool $result) => BackedEnum::one->isNot($target) === $result)
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
    ->expect(fn(mixed $targets, bool $result) => BackedEnum::one->in($targets) === $result)
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
    ->expect(fn(mixed $targets, bool $result) => BackedEnum::one->notIn($targets) === $result)
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
    ->all()
    ->toBe([0 => BackedEnum::one, 2 => BackedEnum::three, 1 => BackedEnum::two]);

it('retrieves a collection of cases sorted by name descending')
    ->expect(BackedEnum::sortDesc())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::two, 2 => BackedEnum::three, 0 => BackedEnum::one]);

it('retrieves a collection of cases sorted by value ascending')
    ->expect(BackedEnum::sortByValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of cases sorted by value descending')
    ->expect(BackedEnum::sortByDescValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([2 => BackedEnum::three, 1 => BackedEnum::two, 0 => BackedEnum::one]);

it('retrieves a collection of cases sorted by a custom value ascending')
    ->expect(BackedEnum::sortBy('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([2 => BackedEnum::three, 1 => BackedEnum::two, 0 => BackedEnum::one]);

it('retrieves a collection of cases sorted by a custom value descending')
    ->expect(BackedEnum::sortByDesc('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves a collection of cases sorted by the result of a closure ascending')
    ->expect(BackedEnum::sortBy(fn(BackedEnum $case) => $case->shape()))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([2 => BackedEnum::three, 1 => BackedEnum::two, 0 => BackedEnum::one]);

it('retrieves a collection of cases sorted by the result of a closure descending')
    ->expect(BackedEnum::sortByDesc(fn(BackedEnum $case) => $case->shape()))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);

it('retrieves the count of cases')
    ->expect(BackedEnum::count())
    ->toBe(3);

it('retrieves the case hydrated from a value')
    ->expect(fn(int $value, BackedEnum $case) => BackedEnum::from($value) === $case)
    ->toBeTrue()
    ->with([
        [1, BackedEnum::one],
        [2, BackedEnum::two],
        [3, BackedEnum::three],
    ]);

it('throws a value error when hydrating backed cases with a missing value', fn() => BackedEnum::from(4))
    ->throwsIf(version_compare(PHP_VERSION, '8.2') == -1, ValueError::class, '4 is not a valid backing value for enum "Cerbero\Enum\BackedEnum"')
    ->throwsIf(version_compare(PHP_VERSION, '8.2') >= 0, ValueError::class, '4 is not a valid backing value for enum Cerbero\Enum\BackedEnum');

it('retrieves the case hydrated from a value or returns null')
    ->expect(fn(int $value, ?BackedEnum $case) => BackedEnum::tryFrom($value) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        [1, BackedEnum::one],
        [2, BackedEnum::two],
        [3, BackedEnum::three],
        [4, null],
    ]);

it('retrieves the case hydrated from a name')
    ->expect(fn(string $name, BackedEnum $case) => BackedEnum::fromName($name) === $case)
    ->toBeTrue()
    ->with([
        ['one', BackedEnum::one],
        ['two', BackedEnum::two],
        ['three', BackedEnum::three],
    ]);

it('throws a value error when hydrating backed cases with a missing name', fn() => BackedEnum::fromName('four'))
    ->throws(ValueError::class, '"four" is not a valid name for enum "Cerbero\Enum\BackedEnum"');

it('retrieves the case hydrated from a name or returns null')
    ->expect(fn(string $name, ?BackedEnum $case) => BackedEnum::tryFromName($name) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['one', BackedEnum::one],
        ['two', BackedEnum::two],
        ['three', BackedEnum::three],
        ['four', null],
    ]);

it('retrieves the cases hydrated from a meta')
    ->expect(fn(string $meta, mixed $value, array $cases) => BackedEnum::fromMeta($meta, $value)->all() === $cases)
    ->toBeTrue()
    ->with([
        ['color', 'red', [BackedEnum::one]],
        ['shape', 'circle', [BackedEnum::three]],
        ['isOdd', true, [BackedEnum::one, BackedEnum::three]],
    ]);

it('retrieves the cases hydrated from a meta using a closure')
    ->expect(BackedEnum::fromMeta('shape', fn(string $shape) => $shape == 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::two]);

it('throws a value error when hydrating cases with an invalid meta', fn() => BackedEnum::fromMeta('color', 'orange'))
    ->throws(ValueError::class, 'Invalid value for the meta "color" for enum "Cerbero\Enum\BackedEnum"');

it('retrieves the case hydrated from a meta or returns null')
    ->expect(fn(string $meta, mixed $value, ?array $cases) => BackedEnum::tryFromMeta($meta, $value)?->all() === $cases)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['color', 'red', [BackedEnum::one]],
        ['isOdd', true, [BackedEnum::one, BackedEnum::three]],
        ['shape', 'rectangle', null],
    ]);

it('attempts to retrieve the case hydrated from a meta using a closure')
    ->expect(BackedEnum::tryFromMeta('shape', fn(string $shape) => $shape == 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([BackedEnum::two]);

it('handles the call to an inaccessible enum method')
    ->expect(BackedEnum::one())
    ->toBe(1);

it('fails handling the call to an invalid enum method', fn() => BackedEnum::four())
    ->throws(ValueError::class, '"four" is not a valid name for enum "Cerbero\Enum\BackedEnum"');

it('runs custom logic when calling an inaccessible enum method', function() {
    Enums::onStaticCall(function(string $enum, string $name, array $arguments) {
        expect($enum)->toBe(BackedEnum::class)
            ->and($name)->toBe('unknownStaticMethod')
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect(BackedEnum::unknownStaticMethod(1, 2, 3))->toBe('ciao');

    (fn() => static::$onStaticCall = null)->bindTo(null, Enums::class)();
});

it('handles the call to an inaccessible case method', fn() => BackedEnum::one->unknownMethod())
    ->throws(Error::class, 'The case Cerbero\Enum\BackedEnum::one has no "unknownMethod" meta set');

it('runs custom logic when calling an inaccessible case method', function() {
    Enums::onCall(function(object $case, string $name, array $arguments) {
        expect($case)->toBeInstanceOf(BackedEnum::class)
            ->and($name)->toBe('unknownMethod')
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect(BackedEnum::one->unknownMethod(1, 2, 3))->toBe('ciao');

    (fn() => self::$onCall = null)->bindTo(null, Enums::class)();
});

it('handles the invocation of a case')
    ->expect((BackedEnum::one)())
    ->toBe(1);

it('runs custom logic when invocating a case', function() {
    Enums::onInvoke(function(object $case, mixed ...$arguments) {
        expect($case)->toBeInstanceOf(BackedEnum::class)
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect((BackedEnum::one)(1, 2, 3))->toBe('ciao');

    (fn() => self::$onInvoke = null)->bindTo(null, Enums::class)();
});

it('retrieves the meta names of an enum', function() {
    expect(BackedEnum::metaNames())->toBe(['color', 'shape', 'isOdd']);
});

it('retrieves the meta attribute names of an enum', function() {
    expect(BackedEnum::metaAttributeNames())->toBe(['color', 'shape']);
});

it('retrieves a case item')
    ->expect(fn(string $item, mixed $value) => BackedEnum::one->resolveItem($item) === $value)
    ->toBeTrue()
    ->with([
        ['name', 'one'],
        ['value', 1],
        ['color', 'red'],
        ['shape', 'triangle'],
    ]);

it('retrieves the item of a case using a closure')
    ->expect(BackedEnum::one->resolveItem(fn(BackedEnum $case) => $case->color()))
    ->toBe('red');

it('throws a value error when attempting to retrieve an invalid item', fn() => BackedEnum::one->resolveItem('invalid'))
    ->throws(ValueError::class, 'The case Cerbero\Enum\BackedEnum::one has no "invalid" meta set');

it('retrieves the value of a backed case or the name of a pure case', function() {
    expect(BackedEnum::one->value())->toBe(1);
});
