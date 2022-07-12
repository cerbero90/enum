<?php

namespace Cerbero\Enum\Concerns;

/**
 * The trait to compare cases of an enum.
 *
 */
trait Compares
{
    /**
     * Determine whether the enum has the given target
     *
     * @param mixed $target
     * @return bool
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
     * Determine whether the enum does not have the given target
     *
     * @param mixed $target
     * @return bool
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
     * Determine whether the current case matches the given target
     *
     * @param mixed $target
     * @return bool
     */
    public function is(mixed $target): bool
    {
        return in_array($target, [$this, static::isPure() ? $this->name : $this->value], true);
    }

    /**
     * Determine whether the current case does not match the given target
     *
     * @param mixed $target
     * @return bool
     */
    public function isNot(mixed $target): bool
    {
        return !$this->is($target);
    }

    /**
     * Determine whether the current case matches at least one of the given targets
     *
     * @param iterable $targets
     * @return bool
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
     * Determine whether the current case does not match any of the given targets
     *
     * @param iterable $targets
     * @return bool
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
