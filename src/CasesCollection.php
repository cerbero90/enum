<?php

namespace Cerbero\Enum;

use BackedEnum;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * The collection of enum cases.
 *
 * @template TKey of array-key
 * @template TValue
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
        $this->enumIsBacked = reset($cases) instanceof BackedEnum;
    }

    /**
     * Retrieve the count of cases.
     */
    public function count(): int
    {
        return count($this->cases);
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
     * Retrieve all the cases as a plain array.
     *
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->cases;
    }

    /**
     * Retrieve all the cases as a plain array recursively.
     *
     * @return array<TKey, mixed>
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->cases as $key => $value) {
            $array[$key] = $value instanceof self ? $value->toArray() : $value;
        }

        return $array;
    }

    /**
     * Add cases to the collection.
     *
     * @param TValue ...$cases
     */
    public function add(mixed ...$cases): static
    {
        return new static([...$this->cases, ...$cases]);
    }

    /**
     * Retrieve the first case.
     *
     * @param (callable(TValue, TKey): bool)|null $callback
     * @return ?TValue
     */
    public function first(callable $callback = null): mixed
    {
        $callback ??= fn() => true;

        foreach ($this->cases as $key => $case) {
            if ($callback($case, $key)) {
                return $case;
            }
        }

        return null;
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
     * @param (callable(TValue): TPluckValue)|string $value
     * @param (callable(TValue): array-key)|string|null $key
     * @return array<array-key, TPluckValue>
     */
    public function pluck(callable|string $value, callable|string $key = null): array
    {
        $result = [];

        foreach ($this->cases as $case) {
            if ($key === null) {
                $result[] = $case->get($value);
            } else {
                $result[$case->get($key)] = $case->get($value);
            }
        }

        return $result;
    }

    /**
     * Retrieve the result of mapping over the cases.
     *
     * @template TMapValue
     *
     * @param callable(TValue, TKey): TMapValue $callback
     * @return array<TKey, TMapValue>
     */
    public function map(callable $callback): array
    {
        $keys = array_keys($this->cases);
        $values = array_map($callback, $this->cases, $keys);

        return array_combine($keys, $values);
    }

    /**
     * Retrieve the cases keyed by their own name.
     */
    public function keyByName(): static
    {
        return $this->keyBy('name');
    }

    /**
     * Retrieve the cases keyed by the given key.
     *
     * @param (callable(TValue): array-key)|string $key
     */
    public function keyBy(callable|string $key): static
    {
        $keyed = [];

        foreach ($this->cases as $case) {
            $keyed[$case->get($key)] = $case;
        }

        return new static($keyed);
    }

    /**
     * Retrieve the cases keyed by their own value.
     */
    public function keyByValue(): static
    {
        return $this->enumIsBacked ? $this->keyBy('value') : new static([]);
    }

    /**
     * Retrieve the cases grouped by the given key.
     *
     * @param (callable(TValue): array-key)|string $key
     */
    public function groupBy(callable|string $key): static
    {
        $grouped = [];

        foreach ($this->cases as $case) {
            $grouped[$case->get($key)] ??= new static([]);

            $grouped[$case->get($key)]->add($case);
        }

        return new static($grouped);
    }

    /**
     * Retrieve a new collection with the filtered cases.
     *
     * @param (callable(TValue): bool)|string $filter
     */
    public function filter(callable|string $filter): static
    {
        /** @phpstan-ignore method.nonObject */
        $callback = is_callable($filter) ? $filter : fn(mixed $case) => $case->get($filter) === true;

        return new static(array_filter($this->cases, $callback));
    }

    /**
     * Retrieve a new collection of cases having only the given names.
     */
    public function only(string ...$name): static
    {
        return $this->filter(fn(mixed $case) => in_array($case->name, $name));
    }

    /**
     * Retrieve a collection of cases not having the given names.
     */
    public function except(string ...$name): static
    {
        return $this->filter(fn(mixed $case) => !in_array($case->name, $name));
    }

    /**
     * Retrieve a new collection of backed cases having only the given values.
     */
    public function onlyValues(string|int ...$value): static
    {
        return $this->filter(fn(mixed $case) => $this->enumIsBacked && in_array($case->value, $value, true));
    }

    /**
     * Retrieve a new collection of backed cases not having the given values.
     */
    public function exceptValues(string|int ...$value): static
    {
        return $this->filter(fn(mixed $case) => $this->enumIsBacked && !in_array($case->value, $value, true));
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

        uasort($cases, fn(mixed $a, mixed $b) => $a->get($key) <=> $b->get($key));

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

        uasort($cases, fn(mixed $a, mixed $b) => $b->get($key) <=> $a->get($key));

        return new static($cases);
    }

    /**
     * Retrieve a new collection of cases sorted by their own value descending.
     */
    public function sortByDescValue(): static
    {
        return $this->enumIsBacked ? $this->sortByDesc('value') : new static([]);
    }
}
