<?php

use Cerbero\Enum\BackedEnum;
use Cerbero\Enum\CasesCollection;
use Cerbero\Enum\PureEnum;

it('retrieves all the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->cases()
    ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);

it('retrieves the count of all the cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->count()
    ->toBe(3);

it('retrieves the first case')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->first()
    ->toBe(PureEnum::one);

it('retrieves the first case with a closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->first(fn (PureEnum $case) => !$case->odd())
    ->toBe(PureEnum::two);

it('returns null if no case is present')
    ->expect(new CasesCollection([]))
    ->first()
    ->toBeNull();

it('retrieves the cases keyed by name')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyByName()
    ->toBe(['one' => PureEnum::one, 'two' => PureEnum::two, 'three' => PureEnum::three]);

it('retrieves the cases keyed by a custom key')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyBy('color')
    ->toBe(['red' => PureEnum::one, 'green' => PureEnum::two, 'blue' => PureEnum::three]);

it('retrieves the cases keyed by a custom closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyBy(fn (PureEnum $case) => $case->shape())
    ->toBe(['triangle' => PureEnum::one, 'square' => PureEnum::two, 'circle' => PureEnum::three]);

it('retrieves the cases keyed by value')
    ->expect(new CasesCollection(BackedEnum::cases()))
    ->keyByValue()
    ->toBe([1 => BackedEnum::one, 2 => BackedEnum::two, 3 => BackedEnum::three]);

it('retrieves an empty array when trying to key cases by value belonging to a pure enum')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keyByValue()
    ->toBeEmpty();

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

it('retrieves all the values of a particular key for all cases')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keys('color')
    ->toBe(['red', 'green', 'blue']);

it('retrieves all the values of a particular key for all cases with a closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->keys(fn (PureEnum $case) => $case->shape())
    ->toBe(['triangle', 'square', 'circle']);

it('retrieves a list of names by default when plucking a pure enum')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck()
    ->toBe(['one', 'two', 'three']);

it('retrieves a list of names by default when plucking a backed enum')
    ->expect(new CasesCollection(BackedEnum::cases()))
    ->pluck()
    ->toBe([1, 2, 3]);

it('retrieves a list of custom values when plucking with an argument')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck('color')
    ->toBe(['red', 'green', 'blue']);

it('retrieves a list of custom values when plucking with a closure')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck(fn (PureEnum $case) => $case->shape())
    ->toBe(['triangle', 'square', 'circle']);

it('retrieves an associative array with custom values and keys when plucking with arguments')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck('shape', 'color')
    ->toBe(['red' => 'triangle', 'green' => 'square', 'blue' => 'circle']);

it('retrieves an associative array with custom values and keys when plucking with closures')
    ->expect(new CasesCollection(PureEnum::cases()))
    ->pluck(fn (PureEnum $case) => $case->shape(), fn (PureEnum $case) => $case->color())
    ->toBe(['red' => 'triangle', 'green' => 'square', 'blue' => 'circle']);

it('retrieves a collection with filtered cases', function () {
    expect((new CasesCollection(PureEnum::cases()))->filter(fn (PureEnum $case) => $case->odd()))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::one, PureEnum::three]);
});

it('retrieves a collection of cases with the given names', function () {
    expect((new CasesCollection(PureEnum::cases()))->only('one', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::one, PureEnum::three]);
});

it('retrieves a collection of cases excluding the given names', function () {
    expect((new CasesCollection(PureEnum::cases()))->except('one', 'three'))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::two]);
});

it('retrieves a collection of cases with the given values', function () {
    expect((new CasesCollection(BackedEnum::cases()))->onlyValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([BackedEnum::one, BackedEnum::three]);
});

it('retrieves a collection of cases excluding the given values', function () {
    expect((new CasesCollection(BackedEnum::cases()))->exceptValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([BackedEnum::two]);
});

it('retrieves an empty collection of cases when when including values of pure enums', function () {
    expect((new CasesCollection(PureEnum::cases()))->onlyValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBeEmpty();
});

it('retrieves an empty collection of cases when when excluding values of pure enums', function () {
    expect((new CasesCollection(PureEnum::cases()))->exceptValues(1, 3))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBeEmpty();
});

it('retrieves a collection of cases sorted by name ascending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sort())
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::one, PureEnum::three, PureEnum::two]);
});

it('retrieves a collection of cases sorted by name decending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortDesc())
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::two, PureEnum::three, PureEnum::one]);
});

it('retrieves a collection of cases sorted by a key ascending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::three, PureEnum::two, PureEnum::one]);
});

it('retrieves a collection of cases sorted by a key decending', function () {
    expect((new CasesCollection(PureEnum::cases()))->sortDescBy('color'))
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([PureEnum::one, PureEnum::two, PureEnum::three]);
});

it('retrieves a collection of cases sorted by value ascending', function () {
    expect((new CasesCollection(BackedEnum::cases()))->sortByValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([BackedEnum::one, BackedEnum::two, BackedEnum::three]);
});

it('retrieves a collection of cases sorted by value decending', function () {
    expect((new CasesCollection(BackedEnum::cases()))->sortDescByValue())
        ->toBeInstanceOf(CasesCollection::class)
        ->cases()
        ->toBe([BackedEnum::three, BackedEnum::two, BackedEnum::one]);
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
