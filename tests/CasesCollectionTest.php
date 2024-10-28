<?php

use Cerbero\Enum\BackedEnum;
use Cerbero\Enum\CasesCollection;
use Cerbero\Enum\PureEnum;
use Pest\Expectation;

it('turns into a JSON with pure cases', function() {
    expect((string) new CasesCollection(PureEnum::cases()))
        ->toBe('["one","two","three"]');
});

it('turns into a JSON with backed cases', function() {
    expect((string) new CasesCollection(BackedEnum::cases()))
        ->toBe('[1,2,3]');
});

it('retrieves all the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->all()
    ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);

it('retrieves the count of all the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->count()
    ->toBe(3);

it('retrieves all the cases as a plain array recursively')
    ->expect((new CasesCollection(PureEnum::cases()))->groupBy('isOdd'))
    ->toArray()
    ->toBe([1 => [PureEnum::one, PureEnum::three], 0 => [PureEnum::two]]);

it('retrieves the first case')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->first()
    ->toBe(PureEnum::one);

it('retrieves the first case with a closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->first(fn(PureEnum $case) => !$case->isOdd())
    ->toBe(PureEnum::two);

it('returns null if no case is present')
    ->expect(new CasesCollection([]))
    ->first()
    ->toBeNull();

it('retrieves the result of mapping over the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->map(fn(PureEnum $case) => $case->color())
    ->toBe(['red', 'green', 'blue']);

it('retrieves the cases keyed by name')
    ->expect((new CasesCollection(PureEnum::cases()))->keyByName())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe(['one' => PureEnum::one, 'two' => PureEnum::two, 'three' => PureEnum::three]);

it('retrieves the cases keyed by a custom key')
    ->expect((new CasesCollection(PureEnum::cases()))->keyBy('color'))
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe(['red' => PureEnum::one, 'green' => PureEnum::two, 'blue' => PureEnum::three]);

it('retrieves the cases keyed by a custom closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyBy(fn(PureEnum $case) => $case->shape())
    ->toBeInstanceOf(CasesCollection::class)
    ->sequence(
        fn(Expectation $case, Expectation $key) => $key->toBe('triangle')->and($case)->toBe(PureEnum::one),
        fn(Expectation $case, Expectation $key) => $key->toBe('square')->and($case)->toBe(PureEnum::two),
        fn(Expectation $case, Expectation $key) => $key->toBe('circle')->and($case)->toBe(PureEnum::three),
    );

it('retrieves the cases keyed by value xxxxxxx')
    ->expect((new CasesCollection(BackedEnum::cases()))->keyByValue())
    ->toBeInstanceOf(CasesCollection::class)
    ->all()
    ->toBe([1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]);

it('retrieves an empty array when trying to key cases by value belonging to a pure enum')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyByValue()
    ->toBeEmpty();

it('retrieves the cases grouped by a custom key')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->groupBy('color')
    ->toBeInstanceOf(CasesCollection::class)
    ->sequence(
        fn(Expectation $cases, Expectation $key) => $key->toBe('red')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::one]),
        fn(Expectation $cases, Expectation $key) => $key->toBe('green')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::two]),
        fn(Expectation $cases, Expectation $key) => $key->toBe('blue')->and($cases)->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::three]),
    );

it('retrieves the cases grouped by a custom closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->groupBy(fn(PureEnum $case) => $case->isOdd())
    ->toBeInstanceOf(CasesCollection::class)
    ->sequence(
        fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::one, PureEnum::three]),
        fn(Expectation $cases) => $cases->toBeInstanceOf(CasesCollection::class)->all()->toBe([PureEnum::two]),
    );

it('retrieves all the names of the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->names()
    ->toBe(['one', 'two', 'three']);

it('retrieves all the values of the cases')
    ->expect(new CasesCollection(BackedEnum::cases()))
    ->values()
    ->toBe([1, 2, 3]);

it('retrieves an empty array when trying to retrieve values belonging to a pure enum')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->values()
    ->toBeEmpty();

it('retrieves a list of names')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck('name')
    ->toBe(['one', 'two', 'three']);

it('retrieves a list of values')
    ->expect(new CasesCollection(BackedEnum::cases()))
    ->pluck('value')
    ->toBe([1, 2, 3]);

it('retrieves a list of custom values when plucking with an argument')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck('color')
    ->toBe(['red', 'green', 'blue']);

it('retrieves a list of custom values when plucking with a closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck(fn(PureEnum $case) => $case->shape())
    ->toBe(['triangle', 'square', 'circle']);

it('retrieves an associative array with custom values and keys when plucking with arguments')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck('shape', 'color')
    ->toBe(['red' => 'triangle', 'green' => 'square', 'blue' => 'circle']);

it('retrieves an associative array with custom values and keys when plucking with closures')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck(fn(PureEnum $case) => $case->shape(), fn(PureEnum $case) => $case->color())
    ->toBe(['red' => 'triangle', 'green' => 'square', 'blue' => 'circle']);

it('retrieves a collection with filtered cases', function () {
    expect((new CasesCollection(PureEnum::cases()))->filter(fn(PureEnum $case) => $case->isOdd()))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three]);
});

it('retrieves a collection with cases filtered by a key', function () {
    expect((new CasesCollection(PureEnum::cases()))->filter('isOdd'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three]);
});

it('retrieves a collection of cases with the given names', function () {
    expect((new CasesCollection(PureEnum::cases()))->only('one', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three]);
});

it('retrieves a collection of cases excluding the given names', function () {
    expect((new CasesCollection(PureEnum::cases()))->except('one', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => PureEnum::two]);
});

it('retrieves a collection of cases with the given values', function () {
    expect((new CasesCollection(BackedEnum::cases()))->onlyValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => BackedEnum::one, 2 => BackedEnum::three]);
});

it('retrieves a collection of cases excluding the given values', function () {
    expect((new CasesCollection(BackedEnum::cases()))->exceptValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => BackedEnum::two]);
});

it('retrieves an empty collection of cases when when including values of pure enums', function () {
    expect((new CasesCollection(PureEnum::cases()))->onlyValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves an empty collection of cases when when excluding values of pure enums', function () {
    expect((new CasesCollection(PureEnum::cases()))->exceptValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBeEmpty();
});

it('retrieves a collection of cases sorted by name ascending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sort())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([0 => PureEnum::one, 2 => PureEnum::three, 1 => PureEnum::two]);
});

it('retrieves a collection of cases sorted by name decending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortDesc())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([1 => PureEnum::two, 2 => PureEnum::three, 0 => PureEnum::one]);
});

it('retrieves a collection of cases sorted by a key ascending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([2 => PureEnum::three, 1 => PureEnum::two, 0 => PureEnum::one]);
});

it('retrieves a collection of cases sorted by a key decending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortByDesc('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);
});

it('retrieves a collection of cases sorted by value ascending', function () {
    expect((new CasesCollection(BackedEnum::cases()))->sortByValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);
});

it('retrieves a collection of cases sorted by value decending', function () {
    expect((new CasesCollection(BackedEnum::cases()))->sortByDescValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->all()
        ->toBe([2 => BackedEnum::three, 1 => BackedEnum::two, 0 => BackedEnum::one]);
});

it('retrieves the iterator', function () {
    expect((new CasesCollection(PureEnum::cases()))->getIterator())
        ->toBeInstanceOf(Traversable::class);
});

it('iterates cases within a loop', function () {
    $i = 0;
    $collection = new CasesCollection(PureEnum::cases());
    $expected = [PureEnum::one, PureEnum::two, PureEnum::three];

    foreach ($collection as $case) {
        expect($case)->toBe($expected[$i++]);
    }
});
