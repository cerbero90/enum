<?php

namespace Cerbero\Enum\Concerns;

use Throwable;
use ValueError;

/**
 * The trait to make cases aware of their own keys.
 *
 */
trait KeysAware
{
    /**
     * Retrieve the given key of the current case
     *
     * @param callable|string $key
     * @return mixed
     * @throws ValueError
     */
    public function get(callable|string $key): mixed
    {
        try {
            return is_callable($key) ? $key($this) : ($this->$key ?? $this->$key());
        } catch (Throwable) {
            $target = is_callable($key) ? 'The given callable' : "\"{$key}\"";
            throw new ValueError(sprintf('%s is not a valid key for enum "%s"', $target, static::class));
        }
    }
}
