<?php

namespace Cerbero\Enum\Concerns;

use BackedEnum;

/**
 * The trait to make an enum self-aware.
 *
 */
trait SelfAware
{
    /**
     * Determine whether the enum is pure
     *
     * @return bool
     */
    public static function isPure(): bool
    {
        return !static::isBacked();
    }

    /**
     * Determine whether the enum is backed
     *
     * @return bool
     */
    public static function isBacked(): bool
    {
        return is_subclass_of(static::class, BackedEnum::class);
    }
}
