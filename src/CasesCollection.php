<?php

namespace Cerbero\Enum;

use BackedEnum;
use Countable;
use IteratorAggregate;
use Traversable;
use UnitEnum;

/**
 * The collection of enum cases.
 *
 * @template TKey of array-key
 * @template-covariant TValue of UnitEnum|BackedEnum
 *
 * @implements IteratorAggregate<TKey, TValue>
 */
class CasesCollection implements Countable, IteratorAggregate
{
    /**
     * Whether the cases belong to a backed enum.
     */
    protected readonly bool $enumIsBacked;

    /**
     * Instantiate the class.
     *
     * @param array<TKey, TValue> $cases
     */
    final public function __construct(protected array $cases)
    {
        $this->enumIsBacked = $this->first() instanceof BackedEnum;
    }

    /**
     * Retrieve the iterable cases.
     *
     * @return Traversable<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        yield from $this->cases;
    }

    /**
     * Retrieve the cases.
     *
     * @return array<TKey, TValue>
     */
    public function cases(): array
    {
        return $this->cases;
    }

    /**
     * Retrieve the count of cases.
     */
    public function count(): int
    {
        return count($this->cases);
    }

    /**
     * Retrieve the first case.
     *
     * @template TFirstDefault
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @param TFirstDefault $default
     * @return TValue|TFirstDefault
     */
    public function first(callable $callback = null, mixed $default = null): mixed
    {
        $callback ??= fn() => true;

        foreach ($this->cases as $key => $case) {
            if ($callback($case, $key)) {
                return $case;
            }
        }

        return $default;
    }

    /**
     * Retrieve the cases keyed by their own name.
     *
     * @return array<string, TValue>
     */
    public function keyByName(): array
    {
        return $this->keyBy('name');
    }

    /**
     * Retrieve the cases keyed by the given key.
     *
     * @param (callable(TValue): array-key)|string $key
     * @return array<array-key, TValue>
     */
    public function keyBy(callable|string $key): array
    {
        $result = [];

        foreach ($this->cases as $case) {
            $result[$case->get($key)] = $case; // @phpstan-ignore method.notFound
        }

        return $result;
    }

    /**
     * Retrieve the cases keyed by their own value.
     *
     * @return array<string|int, TValue>
     */
    public function keyByValue(): array
    {
        return $this->enumIsBacked ? $this->keyBy('value') : [];
    }

    /**
     * Retrieve the cases grouped by the given key.
     *
     * @param (callable(TValue): array-key)|string $key
     * @return array<array-key, TValue[]>
     */
    public function groupBy(callable|string $key): array
    {
        $result = [];

        foreach ($this->cases as $case) {
            $result[$case->get($key)][] = $case; // @phpstan-ignore method.notFound
        }

        return $result;
    }

    /**
     * Retrieve all the names of the cases.
     *
     * @return string[]
     */
    public function names(): array
    {
        return array_column($this->cases, 'name');
    }

    /**
     * Retrieve all the values of the backed cases.
     *
     * @return list<string|int>
     */
    public function values(): array
    {
        return array_column($this->cases, 'value');
    }

    /**
     * Retrieve an array of values optionally keyed by the given key.
     *
     * @template TPluckValue
     *
     * @param (callable(TValue): array-key)|string|null $value
     * @param (callable(TValue): TPluckValue)|string|null $key
     * @return array<array-key, TPluckValue>
     */
    public function pluck(callable|string $value = null, callable|string $key = null): array
    {
        $result = [];
        $value ??= $this->enumIsBacked ? 'value' : 'name';

        foreach ($this->cases as $case) {
            if ($key === null) {
                $result[] = $case->get($value); // @phpstan-ignore method.notFound
            } else {
                $result[$case->get($key)] = $case->get($value); // @phpstan-ignore-line
            }
        }

        return $result;
    }

    /**
     * Retrieve a new collection with the filtered cases.
     *
     * @param (callable(TValue): bool)|string $filter
     */
    public function filter(callable|string $filter): static
    {
        // @phpstan-ignore-next-line
        $callback = is_callable($filter) ? $filter : fn(mixed $case) => $case->get($filter) === true;

        return new static(array_filter($this->cases, $callback));
    }

    /**
     * Retrieve a new collection of cases having only the given names.
     */
    public function only(string ...$name): static
    {
        return $this->filter(fn(UnitEnum $case) => in_array($case->name, $name));
    }

    /**
     * Retrieve a collection of cases not having the given names.
     */
    public function except(string ...$name): static
    {
        return $this->filter(fn(UnitEnum $case) => !in_array($case->name, $name));
    }

    /**
     * Retrieve a new collection of backed cases having only the given values.
     */
    public function onlyValues(string|int ...$value): static
    {
        return $this->filter(function (UnitEnum $case) use ($value) {
            /** @var BackedEnum $case */
            return $this->enumIsBacked && in_array($case->value, $value, true);
        });
    }

    /**
     * Retrieve a new collection of backed cases not having the given values.
     */
    public function exceptValues(string|int ...$value): static
    {
        return $this->filter(function (UnitEnum $case) use ($value) {
            /** @var BackedEnum $case */
            return $this->enumIsBacked && !in_array($case->value, $value, true);
        });
    }

    /**
     * Retrieve a new collection of cases sorted by their own name ascending.
     */
    public function sort(): static
    {
        return $this->sortBy('name');
    }

    /**
     * Retrieve a new collection of cases sorted by the given key ascending.
     *
     * @param (callable(TValue): mixed)|string $key
     */
    public function sortBy(callable|string $key): static
    {
        $cases = $this->cases;

        uasort($cases, fn(mixed $a, mixed $b) => $a->get($key) <=> $b->get($key)); // @phpstan-ignore-line

        return new static($cases);
    }

    /**
     * Retrieve a new collection of cases sorted by their own name descending.
     */
    public function sortDesc(): static
    {
        return $this->sortByDesc('name');
    }

    /**
     * Retrieve a new collection of cases sorted by the given key descending.
     *
     * @param (callable(TValue): mixed)|string $key
     */
    public function sortByDesc(callable|string $key): static
    {
        $cases = $this->cases;

        uasort($cases, fn(mixed $a, mixed $b) => $a->get($key) > $b->get($key) ? -1 : 1); // @phpstan-ignore-line

        return new static($cases);
    }

    /**
     * Retrieve a new collection of cases sorted by their own value ascending.
     */
    public function sortByValue(): static
    {
        return $this->enumIsBacked ? $this->sortBy('value') : new static([]);
    }

    /**
     * Retrieve a new collection of cases sorted by their own value descending.
     */
    public function sortByDescValue(): static
    {
        return $this->enumIsBacked ? $this->sortByDesc('value') : new static([]);
    }
}
