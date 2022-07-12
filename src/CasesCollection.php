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
 */
class CasesCollection implements Countable, IteratorAggregate
{
    /**
     * Whether the cases belong to a backed enum
     *
     * @var bool
     */
    protected bool $enumIsBacked;

    /**
     * Instantiate the class
     *
     * @param array $cases
     */
    public function __construct(protected array $cases)
    {
        $this->enumIsBacked = $this->first() instanceof BackedEnum;
    }

    /**
     * Retrieve the iterable cases
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return (function () {
            foreach ($this->cases as $case) {
                yield $case;
            }
        })();
    }

    /**
     * Retrieve the cases
     *
     * @return array
     */
    public function cases(): array
    {
        return $this->cases;
    }

    /**
     * Retrieve the count of cases
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->cases);
    }

    /**
     * Retrieve the first case
     *
     * @param callable|null $callback
     * @param mixed $default
     * @return mixed
     */
    public function first(callable $callback = null, mixed $default = null): mixed
    {
        $callback ??= fn () => true;

        foreach ($this->cases as $case) {
            if ($callback($case)) {
                return $case;
            }
        }

        return $default;
    }

    /**
     * Retrieve the cases keyed by name
     *
     * @return array<string, mixed>
     */
    public function keyByName(): array
    {
        return $this->keyBy('name');
    }

    /**
     * Retrieve the cases keyed by the given key
     *
     * @param callable|string $key
     * @return array<string, mixed>
     */
    public function keyBy(callable|string $key): array
    {
        $result = [];

        foreach ($this->cases as $case) {
            $result[$case->get($key)] = $case;
        }

        return $result;
    }

    /**
     * Retrieve the cases keyed by value
     *
     * @return array<string, mixed>
     */
    public function keyByValue(): array
    {
        return $this->enumIsBacked ? $this->keyBy('value') : [];
    }

    /**
     * Retrieve the cases grouped by the given key
     *
     * @param callable|string $key
     * @return array<string, mixed>
     */
    public function groupBy(callable|string $key): array
    {
        $result = [];

        foreach ($this->cases as $case) {
            $result[$case->get($key)][] = $case;
        }

        return $result;
    }

    /**
     * Retrieve all the names of the cases
     *
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_column($this->cases, 'name');
    }

    /**
     * Retrieve all the values of the backed cases
     *
     * @return array<int, string|int>
     */
    public function values(): array
    {
        return array_column($this->cases, 'value');
    }

    /**
     * Retrieve an array of values optionally keyed by the given key
     *
     * @param callable|string|null $value
     * @param callable|string|null $key
     * @return array
     */
    public function pluck(callable|string $value = null, callable|string $key = null): array
    {
        $result = [];
        $value ??= $this->enumIsBacked ? 'value' : 'name';

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
     * Retrieve a collection with the filtered cases
     *
     * @param callable|string $filter
     * @return static
     */
    public function filter(callable|string $filter): static
    {
        $callback = is_callable($filter) ? $filter : fn (mixed $case) => $case->get($filter) === true;
        $cases = array_filter($this->cases, $callback);

        return new static(array_values($cases));
    }

    /**
     * Retrieve a collection of cases having the given names
     *
     * @param string ...$name
     * @return static
     */
    public function only(string ...$name): static
    {
        return $this->filter(fn (UnitEnum $case) => in_array($case->name, $name));
    }

    /**
     * Retrieve a collection of cases not having the given names
     *
     * @param string ...$name
     * @return static
     */
    public function except(string ...$name): static
    {
        return $this->filter(fn (UnitEnum $case) => !in_array($case->name, $name));
    }

    /**
     * Retrieve a collection of backed cases having the given values
     *
     * @param string|int ...$value
     * @return static
     */
    public function onlyValues(string|int ...$value): static
    {
        return $this->filter(fn (UnitEnum $case) => $this->enumIsBacked && in_array($case->value, $value, true));
    }

    /**
     * Retrieve a collection of backed cases not having the given values
     *
     * @param string|int ...$value
     * @return static
     */
    public function exceptValues(string|int ...$value): static
    {
        return $this->filter(fn (UnitEnum $case) => $this->enumIsBacked && !in_array($case->value, $value, true));
    }

    /**
     * Retrieve a collection of cases sorted by name ascending
     *
     * @return static
     */
    public function sort(): static
    {
        return $this->sortBy('name');
    }

    /**
     * Retrieve a collection of cases sorted by name descending
     *
     * @return static
     */
    public function sortDesc(): static
    {
        return $this->sortDescBy('name');
    }

    /**
     * Retrieve a collection of cases sorted by the given key ascending
     *
     * @param callable|string $key
     * @return static
     */
    public function sortBy(callable|string $key): static
    {
        $cases = $this->cases;

        usort($cases, fn ($a, $b) => $a->get($key) <=> $b->get($key));

        return new static($cases);
    }

    /**
     * Retrieve a collection of cases sorted by the given key descending
     *
     * @param callable|string $key
     * @return static
     */
    public function sortDescBy(callable|string $key): static
    {
        $cases = $this->cases;

        usort($cases, fn ($a, $b) => $a->get($key) > $b->get($key) ? -1 : 1);

        return new static($cases);
    }

    /**
     * Retrieve a collection of cases sorted by value ascending
     *
     * @return static
     */
    public function sortByValue(): static
    {
        return $this->enumIsBacked ? $this->sortBy('value') : new static([]);
    }

    /**
     * Retrieve a collection of cases sorted by value descending
     *
     * @return static
     */
    public function sortDescByValue(): static
    {
        return $this->enumIsBacked ? $this->sortDescBy('value') : new static([]);
    }
}
