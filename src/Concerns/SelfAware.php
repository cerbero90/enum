<?php

namespace Cerbero\Enum\Concerns;

use BackedEnum;
use Throwable;
use ValueError;

/**
 * The trait to make an enum self-aware.
 */
trait SelfAware
{
    /**
     * Determine whether the enum is pure.
     */
    public static function isPure(): bool
    {
        return !static::isBacked();
    }

    /**
     * Determine whether the enum is backed.
     */
    public static function isBacked(): bool
    {
        return is_subclass_of(static::class, BackedEnum::class);
    }

    /**
     * Retrieve the given key of this case.
     *
     * @template TGetValue
     *
     * @param (callable(self): TGetValue)|string $key
     * @return TGetValue
     * @throws ValueError
     */
    public function get(callable|string $key): mixed
    {
        try {
            return is_callable($key) ? $key($this) : ($this->$key ?? $this->$key());
        } catch (Throwable) {
            $target = is_callable($key) ? 'The given callable' : "\"{$key}\"";

            throw new ValueError(sprintf('%s is not a valid key for the enum "%s"', $target, static::class));
        }
    }
}
