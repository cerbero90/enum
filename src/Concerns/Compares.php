<?php

namespace Cerbero\Enum\Concerns;

/**
 * The trait to compare the cases of an enum.
 */
trait Compares
{
    /**
     * Determine whether the enum includes the given target.
     */
    public static function has(mixed $target): bool
    {
        foreach (static::cases() as $case) {
            if ($case->is($target)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the enum does not include the given target.
     */
    public static function doesntHave(mixed $target): bool
    {
        foreach (static::cases() as $case) {
            if ($case->is($target)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine whether this case matches the given target.
     */
    public function is(mixed $target): bool
    {
        return in_array($target, [$this, static::isPure() ? $this->name : $this->value], true);
    }

    /**
     * Determine whether this case does not match the given target.
     */
    public function isNot(mixed $target): bool
    {
        return !$this->is($target);
    }

    /**
     * Determine whether this case matches at least one of the given targets.
     *
     * @param iterable<array-key, mixed> $targets
     */
    public function in(iterable $targets): bool
    {
        foreach ($targets as $target) {
            if ($this->is($target)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether this case does not match any of the given targets.
     *
     * @param iterable<array-key, mixed> $targets
     */
    public function notIn(iterable $targets): bool
    {
        foreach ($targets as $target) {
            if ($this->is($target)) {
                return false;
            }
        }

        return true;
    }
}
