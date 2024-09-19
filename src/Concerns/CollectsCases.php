<?php

namespace Cerbero\Enum\Concerns;

use Cerbero\Enum\CasesCollection;

/**
 * The trait to collect the cases of an enum.
 */
trait CollectsCases
{
    /**
     * Retrieve a collection with all the cases.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function collect(): CasesCollection
    {
        return new CasesCollection(self::cases());
    }

    /**
     * Retrieve the count of cases.
     */
    public static function count(): int
    {
        return self::collect()->count();
    }

    /**
     * Retrieve the first case.
     *
     * @param (callable(self, array-key): bool)|null $callback
     */
    public function first(callable $callback = null): ?self
    {
        return self::collect()->first($callback);
    }

    /**
     * Retrieve the result of mapping over all the cases.
     *
     * @template TMapValue
     *
     * @param callable(self, array-key): TMapValue $callback
     * @return array<array-key, TMapValue>
     */
    public function map(callable $callback): array
    {
        return self::collect()->map($callback);
    }

    /**
     * Retrieve all the cases keyed by their own name.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function keyByName(): CasesCollection
    {
        return self::collect()->keyByName();
    }

    /**
     * Retrieve all the cases keyed by the given key.
     *
     * @param (callable(self): array-key)|string $key
     * @return CasesCollection<array-key, self>
     */
    public static function keyBy(callable|string $key): CasesCollection
    {
        return self::collect()->keyBy($key);
    }

    /**
     * Retrieve all the cases keyed by their own value.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function keyByValue(): CasesCollection
    {
        return self::collect()->keyByValue();
    }

    /**
     * Retrieve all the cases grouped by the given key.
     *
     * @param (callable(self): array-key)|string $key
     * @return CasesCollection<array-key, CasesCollection<array-key, self>>
     */
    public static function groupBy(callable|string $key): CasesCollection
    {
        return self::collect()->groupBy($key);
    }

    /**
     * Retrieve the name of all the cases.
     *
     * @return string[]
     */
    public static function names(): array
    {
        return self::collect()->names();
    }

    /**
     * Retrieve the value of all the backed cases.
     *
     * @return list<string|int>
     */
    public static function values(): array
    {
        return self::collect()->values();
    }

    /**
     * Retrieve an array of values optionally keyed by the given key.
     *
     * @template TPluckValue
     *
     * @param (callable(self): TPluckValue)|string $value
     * @param (callable(self): array-key)|string|null $key
     * @return array<array-key, TPluckValue>
     */
    public static function pluck(callable|string $value, callable|string $key = null): array
    {
        return self::collect()->pluck($value, $key);
    }

    /**
     * Retrieve only the filtered cases.
     *
     * @param (callable(self): bool)|string $filter
     * @return CasesCollection<array-key, self>
     */
    public static function filter(callable|string $filter): CasesCollection
    {
        return self::collect()->filter($filter);
    }

    /**
     * Retrieve only the cases having the given names.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function only(string ...$names): CasesCollection
    {
        return self::collect()->only(...$names);
    }

    /**
     * Retrieve only the cases not having the given names.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function except(string ...$names): CasesCollection
    {
        return self::collect()->except(...$names);
    }

    /**
     * Retrieve only the cases having the given values.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function onlyValues(string|int ...$values): CasesCollection
    {
        return self::collect()->onlyValues(...$values);
    }

    /**
     * Retrieve only the cases not having the given values.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function exceptValues(string|int ...$values): CasesCollection
    {
        return self::collect()->exceptValues(...$values);
    }

    /**
     * Retrieve all the cases sorted by their own name ascending.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function sort(): CasesCollection
    {
        return self::collect()->sort();
    }

    /**
     * Retrieve all the cases sorted by the given key ascending.
     *
     * @param (callable(self): mixed)|string $key
     * @return CasesCollection<array-key, self>
     */
    public static function sortBy(callable|string $key): CasesCollection
    {
        return self::collect()->sortBy($key);
    }

    /**
     * Retrieve all the cases sorted by their own value ascending.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function sortByValue(): CasesCollection
    {
        return self::collect()->sortByValue();
    }

    /**
     * Retrieve all the cases sorted by their own name descending.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function sortDesc(): CasesCollection
    {
        return self::collect()->sortDesc();
    }

    /**
     * Retrieve all the cases sorted by the given key descending.
     *
     * @param (callable(self): mixed)|string $key
     * @return CasesCollection<array-key, self>
     */
    public static function sortByDesc(callable|string $key): CasesCollection
    {
        return self::collect()->sortByDesc($key);
    }

    /**
     * Retrieve all the cases sorted by their own value descending.
     *
     * @return CasesCollection<array-key, self>
     */
    public static function sortByDescValue(): CasesCollection
    {
        return self::collect()->sortByDescValue();
    }
}
