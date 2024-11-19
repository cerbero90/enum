<?php

use Cerbero\Enum\CasesCollection;
use Cerbero\Enum\Enums;
use Cerbero\Enum\InvalidMetaAttribute;
use Cerbero\Enum\PureEnum;
use Pest\Expectation;

it('determines whether the enum is pure')
    ->expect(PureEnum::isPure())
    ->toBeTrue();

it('determines whether the enum is backed')
    ->expect(PureEnum::isBacked())
    ->toBeFalse();

it('retrieves a collection with all the cases')
    ->expect(PureEnum::collect())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);

it('retrieves the first case', fn() => expect(PureEnum::first())->toBe(PureEnum::one));

it('retrieves the first case with a closure')
    ->expect(PureEnum::first(fn(PureEnum $case) => !$case->isOdd()))
    ->toBe(PureEnum::two);

it('retrieves the result of mapping over all the cases', function() {
    $cases = $keys = [];

    $mapped = PureEnum::map(function(PureEnum $case, int $key) use (&$cases, &$keys) {
        $cases[] = $case;
        $keys[] = $key;

        return $case->color();
    });

    expect($cases)->toBe([PureEnum::one, PureEnum::two, PureEnum::three])
        ->and($keys)->toBe([0, 1, 2])
        ->and($mapped)->toBe(['red', 'green', 'blue']);
});

it('retrieves all cases keyed by name', function () {
    expect(PureEnum::keyByName())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe(['one' => PureEnum::one, 'two' => PureEnum::two, 'three' => PureEnum::three]);
});

it('retrieves all cases keyed by value', function () {
    expect(PureEnum::keyByValue())
        ->toBeEmpty();
});

it('retrieves all cases keyed by a custom key', function () {
    expect(PureEnum::keyBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe(['red' => PureEnum::one, 'green' => PureEnum::two, 'blue' => PureEnum::three]);
});

it('retrieves all cases keyed by the result of a closure', function () {
    expect(PureEnum::keyBy(fn(PureEnum $case) => $case->shape()))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $case, Expectation $key) => $key->toBe('triangle')->and($case)->toBe(PureEnum::one),
            fn(Expectation $case, Expectation $key) => $key->toBe('square')->and($case)->toBe(PureEnum::two),
            fn(Expectation $case, Expectation $key) => $key->toBe('circle')->and($case)->toBe(PureEnum::three),
        );
});

it('retrieves all cases grouped by a custom key', function () {
    expect(PureEnum::groupBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $cases, Expectation $key) => $key->toBe('red')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::one]),
            fn(Expectation $cases, Expectation $key) => $key->toBe('green')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::two]),
            fn(Expectation $cases, Expectation $key) => $key->toBe('blue')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::three]),
        );
});

it('retrieves all cases grouped by the result of a closure', function () {
    expect(PureEnum::groupBy(fn(PureEnum $case) => $case->isOdd()))
        ->toBeInstanceOf(CasesCollection::class)
        ->sequence(
            fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::one, PureEnum::three]),
            fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::two]),
        );
});

it('retrieves all the names of the cases', function () {
    expect(PureEnum::names())->toBe(['one', 'two', 'three']);
});

it('retrieves all the values of the backed cases', function () {
    expect(PureEnum::values())->toBeEmpty();
});

it('retrieves a collection with the filtered cases', function () {
    expect(PureEnum::filter(fn(UnitEnum $case) => $case->name !== 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::one, PureEnum::two]);
});

it('retrieves a collection with cases filtered by a meta', function () {
    expect(PureEnum::filter('isOdd'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three]);
});

it('retrieves a collection of cases having the given names', function () {
    expect(PureEnum::only('two', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => PureEnum::two, 2 => PureEnum::three]);
});

it('retrieves a collection of cases not having the given names', function () {
    expect(PureEnum::except('one', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => PureEnum::two]);
});

it('retrieves a collection of backed cases having the given values', function () {
    expect(PureEnum::onlyValues(2, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves a collection of backed cases not having the given values', function () {
    expect(PureEnum::exceptValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves an array of names', function () {
    expect(PureEnum::pluck('name'))->toBe(['one', 'two', 'three']);
});

it('retrieves an array of custom values', function () {
    expect(PureEnum::pluck('color'))->toBe(['red', 'green', 'blue']);
});

it('retrieves an associative array with custom keys and values', function () {
    expect(PureEnum::pluck('color', 'shape'))
        ->toBe(['triangle' => 'red', 'square' => 'green', 'circle' => 'blue']);
});

it('retrieves an associative array with keys and values resolved from closures', function () {
    expect(PureEnum::pluck(fn(PureEnum $case) => $case->name, fn(PureEnum $case) => $case->color()))
        ->toBe(['red' => 'one', 'green' => 'two', 'blue' => 'three']);
});

it('determines whether an enum has a target')
    ->expect(fn(mixed $target, bool $result) => PureEnum::has($target) === $result)
    ->toBeTrue()
    ->with([
        [PureEnum::one, true],
        [new stdClass(), false],
        ['one', true],
        ['four', false],
        [1, false],
        [4, false],
        ['1', false],
        ['4', false],
    ]);

it('determines whether an enum does not have a target')
    ->expect(fn(mixed $target, bool $result) => PureEnum::doesntHave($target) === $result)
    ->toBeTrue()
    ->with([
        [PureEnum::one, false],
        [new stdClass(), true],
        ['one', false],
        ['four', true],
        [1, true],
        [4, true],
        ['1', true],
        ['4', true],
    ]);

it('determines whether an enum case matches a target')
    ->expect(fn(mixed $target, bool $result) => PureEnum::one->is($target) === $result)
    ->toBeTrue()
    ->with([
        [PureEnum::one, true],
        [PureEnum::two, false],
        ['one', true],
        ['two', false],
        [1, false],
        [2, false],
        ['1', false],
        ['2', false],
    ]);

it('determines whether an enum case does not match a target')
    ->expect(fn(mixed $target, bool $result) => PureEnum::one->isNot($target) === $result)
    ->toBeTrue()
    ->with([
        [PureEnum::one, false],
        [PureEnum::two, true],
        ['one', false],
        ['two', true],
        [1, true],
        [2, true],
        ['1', true],
        ['2', true],
    ]);

it('determines whether an enum case matches some targets')
    ->expect(fn(mixed $targets, bool $result) => PureEnum::one->in($targets) === $result)
    ->toBeTrue()
    ->with([
        [[PureEnum::one, PureEnum::two], true],
        [[PureEnum::two, PureEnum::three], false],
        [['one', 'two'], true],
        [['two', 'three'], false],
        [[1, 2], false],
        [[2, 3], false],
        [['1', '2'], false],
        [['2', '3'], false],
    ]);

it('determines whether an enum case does not match any target')
    ->expect(fn(mixed $targets, bool $result) => PureEnum::one->notIn($targets) === $result)
    ->toBeTrue()
    ->with([
        [[PureEnum::one, PureEnum::two], false],
        [[PureEnum::two, PureEnum::three], true],
        [['one', 'two'], false],
        [['two', 'three'], true],
        [[1, 2], true],
        [[2, 3], true],
        [['1', '2'], true],
        [['2', '3'], true],
    ]);

it('retrieves a collection of cases sorted by name ascending', function () {
    expect(PureEnum::sort())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three, 1 => PureEnum::two]);
});

it('retrieves a collection of cases sorted by name descending', function () {
    expect(PureEnum::sortDesc())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => PureEnum::two, 2 => PureEnum::three, 0 => PureEnum::one]);
});

it('retrieves a collection of cases sorted by value ascending', function () {
    expect(PureEnum::sortByValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves a collection of cases sorted by value descending', function () {
    expect(PureEnum::sortByDescValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves a collection of cases sorted by a custom value ascending', function () {
    expect(PureEnum::sortBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([2 => PureEnum::three, 1 => PureEnum::two, 0 => PureEnum::one]);
});

it('retrieves a collection of cases sorted by a custom value descending', function () {
    expect(PureEnum::sortByDesc('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);
});

it('retrieves a collection of cases sorted by the result of a closure ascending', function () {
    expect(PureEnum::sortBy(fn(PureEnum $case) => $case->shape()))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([2 => PureEnum::three, 1 => PureEnum::two, 0 => PureEnum::one]);
});

it('retrieves a collection of cases sorted by the result of a closure descending', function () {
    expect(PureEnum::sortByDesc(fn(PureEnum $case) => $case->shape()))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);
});

it('retrieves the count of cases', function () {
    expect(PureEnum::count())->toBe(3);
});

it('retrieves the case hydrated from a value')
    ->expect(fn(string $value, PureEnum $case) => PureEnum::from($value) === $case)
    ->toBeTrue()
    ->with([
        ['one', PureEnum::one],
        ['two', PureEnum::two],
        ['three', PureEnum::three],
    ]);

it('throws a value error when hydrating cases with an invalid value', fn() => PureEnum::from('1'))
    ->throws(ValueError::class, '"1" is not a valid name for enum "Cerbero\Enum\PureEnum"');

it('retrieves the case hydrated from a value or returns null')
    ->expect(fn(string $value, ?PureEnum $case) => PureEnum::tryFrom($value) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['one', PureEnum::one],
        ['two', PureEnum::two],
        ['three', PureEnum::three],
        ['four', null],
    ]);

it('retrieves the case hydrated from a name')
    ->expect(fn(string $name, PureEnum $case) => PureEnum::fromName($name) === $case)
    ->toBeTrue()
    ->with([
        ['one', PureEnum::one],
        ['two', PureEnum::two],
        ['three', PureEnum::three],
    ]);

it('throws a value error when hydrating cases with an invalid name', fn() => PureEnum::fromName('1'))
    ->throws(ValueError::class, '"1" is not a valid name for enum "Cerbero\Enum\PureEnum"');

it('retrieves the case hydrated from a name or returns null')
    ->expect(fn(string $name, ?PureEnum $case) => PureEnum::tryFromName($name) === $case)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['one', PureEnum::one],
        ['two', PureEnum::two],
        ['three', PureEnum::three],
        ['four', null],
    ]);

it('retrieves the cases hydrated from a meta')
    ->expect(fn(string $meta, mixed $value, array $cases) => PureEnum::fromMeta($meta, $value)->all() === $cases)
    ->toBeTrue()
    ->with([
        ['color', 'red', [PureEnum::one]],
        ['shape', 'circle', [PureEnum::three]],
        ['isOdd', true, [PureEnum::one, PureEnum::three]],
    ]);

it('retrieves the cases hydrated from a meta using a closure')
    ->expect(PureEnum::fromMeta('shape', fn(string $meta) => $meta == 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([PureEnum::two]);

it('throws a value error when hydrating cases with an invalid meta', fn() => PureEnum::fromMeta('color', 'orange'))
    ->throws(ValueError::class, 'Invalid value for the meta "color" for enum "Cerbero\Enum\PureEnum"');

it('retrieves the case hydrated from a meta or returns null')
    ->expect(fn(string $meta, mixed $value, ?array $cases) => PureEnum::tryFromMeta($meta, $value)?->all() === $cases)
    ->toBeTrue()
    ->not->toThrow(ValueError::class)
    ->with([
        ['color', 'red', [PureEnum::one]],
        ['isOdd', true, [PureEnum::one, PureEnum::three]],
        ['shape', 'rectangle', null],
    ]);

it('attempts to retrieve the case hydrated from a meta using a closure')
    ->expect(PureEnum::fromMeta('shape', fn(string $meta) => $meta == 'square'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([PureEnum::two]);

it('handles the call to an inaccessible enum method')
    ->expect(PureEnum::one())
    ->toBe('one');

it('fails handling the call to an invalid enum method', fn() => PureEnum::four())
    ->throws(ValueError::class, '"four" is not a valid name for enum "Cerbero\Enum\PureEnum"');

it('runs custom logic when calling an inaccessible enum method', function() {
    Enums::onStaticCall(function(string $enum, string $name, array $arguments) {
        expect($enum)->toBe(PureEnum::class)
            ->and($name)->toBe('unknownStaticMethod')
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect(PureEnum::unknownStaticMethod(1, 2, 3))->toBe('ciao');

    (fn() => self::$onStaticCall = null)->bindTo(null, Enums::class)();
});

it('handles the call to an inaccessible case method', fn() => PureEnum::one->unknownMethod())
    ->throws(Error::class, '"unknownMethod" is not a valid meta for enum "Cerbero\Enum\PureEnum"');

it('runs custom logic when calling an inaccessible case method', function() {
    Enums::onCall(function(object $case, string $name, array $arguments) {
        expect($case)->toBeInstanceOf(PureEnum::class)
            ->and($name)->toBe('unknownMethod')
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect(PureEnum::one->unknownMethod(1, 2, 3))->toBe('ciao');

    (fn() => self::$onCall = null)->bindTo(null, Enums::class)();
});

it('handles the invocation of a case')
    ->expect((PureEnum::one)())
    ->toBe('one');

it('runs custom logic when invocating a case', function() {
    Enums::onInvoke(function(object $case, mixed ...$arguments) {
        expect($case)->toBeInstanceOf(PureEnum::class)
            ->and($arguments)->toBe([1, 2, 3]);

        return 'ciao';
    });

    expect((PureEnum::one)(1, 2, 3))->toBe('ciao');

    (fn() => self::$onInvoke = null)->bindTo(null, Enums::class)();
});

it('retrieves the meta names of an enum', function() {
    expect(PureEnum::metaNames())->toBe(['color', 'shape', 'isOdd']);
});

it('retrieves the meta attribute names of an enum', function() {
    expect(PureEnum::metaAttributeNames())->toBe(['color', 'shape']);
});

it('retrieves the item of a case')
    ->expect(fn(string $item, mixed $value) => PureEnum::one->resolveItem($item) === $value)
    ->toBeTrue()
    ->with([
        ['name', 'one'],
        ['color', 'red'],
        ['shape', 'triangle'],
    ]);

it('retrieves the item of a case using a closure')
    ->expect(PureEnum::one->resolveItem(fn(PureEnum $case) => $case->color()))
    ->toBe('red');

it('throws a value error when attempting to retrieve an invalid item', fn() => PureEnum::one->resolveItem('invalid'))
    ->throws(ValueError::class, '"invalid" is not a valid meta for enum "Cerbero\Enum\PureEnum"');

it('retrieves the value of a backed case or the name of a pure case', function() {
    expect(PureEnum::one->value())->toBe('one');
});

it('fails if a meta attribute does not have a name', fn() => InvalidMetaAttribute::metaNames())
    ->throws(InvalidArgumentException::class, 'The name of meta must be a string');
