<?php

namespace Cerbero\Enum\Concerns;

use Cerbero\Enum\CasesCollection;

/**
 * The trait to collect cases of an enum.
 *
 */
trait CollectsCases
{
    /**
     * Retrieve a collection with all the cases
     *
     * @return CasesCollection
     */
    public static function collect(): CasesCollection
    {
        return new CasesCollection(static::cases());
    }

    /**
     * Retrieve the count of cases
     *
     * @return int
     */
    public static function count(): int
    {
        return static::collect()->count();
    }

    /**
     * Retrieve all cases keyed by name
     *
     * @return array<string, mixed>
     */
    public static function casesByName(): array
    {
        return static::collect()->keyByName();
    }

    /**
     * Retrieve all cases keyed by value
     *
     * @return array<string|int, mixed>
     */
    public static function casesByValue(): array
    {
        return static::collect()->keyByValue();
    }

    /**
     * Retrieve all cases keyed by the given key
     *
     * @param callable|string $key
     * @return array
     */
    public static function casesBy(callable|string $key): array
    {
        return static::collect()->keyBy($key);
    }

    /**
     * Retrieve all cases grouped by the given key
     *
     * @param callable|string $key
     * @return array
     */
    public static function groupBy(callable|string $key): array
    {
        return static::collect()->groupBy($key);
    }

    /**
     * Retrieve all the names of the cases
     *
     * @return array<int, string>
     */
    public static function names(): array
    {
        return static::collect()->names();
    }

    /**
     * Retrieve all the values of the backed cases
     *
     * @return array<int, mixed>
     */
    public static function values(): array
    {
        return static::collect()->values();
    }

    /**
     * Retrieve a collection with the filtered cases
     *
     * @param callable|string $filter
     * @return CasesCollection
     */
    public static function filter(callable|string $filter): CasesCollection
    {
        return static::collect()->filter($filter);
    }

    /**
     * Retrieve a collection of cases having the given names
     *
     * @param string ...$name
     * @return CasesCollection
     */
    public static function only(string ...$name): CasesCollection
    {
        return static::collect()->only(...$name);
    }

    /**
     * Retrieve a collection of cases not having the given names
     *
     * @param string ...$name
     * @return CasesCollection
     */
    public static function except(string ...$name): CasesCollection
    {
        return static::collect()->except(...$name);
    }

    /**
     * Retrieve a collection of backed cases having the given values
     *
     * @param string|int ...$value
     * @return CasesCollection
     */
    public static function onlyValues(string|int ...$value): CasesCollection
    {
        return static::collect()->onlyValues(...$value);
    }

    /**
     * Retrieve a collection of backed cases not having the given values
     *
     * @param string|int ...$value
     * @return CasesCollection
     */
    public static function exceptValues(string|int ...$value): CasesCollection
    {
        return static::collect()->exceptValues(...$value);
    }

    /**
     * Retrieve an array of values optionally keyed by the given key
     *
     * @param callable|string|null $value
     * @param callable|string|null $key
     * @return array
     */
    public static function pluck(callable|string $value = null, callable|string $key = null): array
    {
        return static::collect()->pluck($value, $key);
    }

    /**
     * Retrieve a collection of cases sorted by name ascending
     *
     * @return CasesCollection
     */
    public static function sort(): CasesCollection
    {
        return static::collect()->sort();
    }

    /**
     * Retrieve a collection of cases sorted by name descending
     *
     * @return CasesCollection
     */
    public static function sortDesc(): CasesCollection
    {
        return static::collect()->sortDesc();
    }

    /**
     * Retrieve a collection of cases sorted by value ascending
     *
     * @return CasesCollection
     */
    public static function sortByValue(): CasesCollection
    {
        return static::collect()->sortByValue();
    }

    /**
     * Retrieve a collection of cases sorted by value descending
     *
     * @return CasesCollection
     */
    public static function sortDescByValue(): CasesCollection
    {
        return static::collect()->sortDescByValue();
    }

    /**
     * Retrieve a collection of cases sorted by the given key ascending
     *
     * @param callable|string $key
     * @return CasesCollection
     */
    public static function sortBy(callable|string $key): CasesCollection
    {
        return static::collect()->sortBy($key);
    }

    /**
     * Retrieve a collection of cases sorted by the given key descending
     *
     * @param callable|string $key
     * @return CasesCollection
     */
    public static function sortDescBy(callable|string $key): CasesCollection
    {
        return static::collect()->sortDescBy($key);
    }
}
