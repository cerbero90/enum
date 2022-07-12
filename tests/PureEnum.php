<?php

namespace Cerbero\Enum;

use Cerbero\Enum\Concerns\Enumerates;

/**
 * The pure enum to test.
 *
 */
enum PureEnum
{
    use Enumerates;

    case one;
    case two;
    case three;

    /**
     * Retrieve the color of the case
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            static::one => 'red',
            static::two => 'green',
            static::three => 'blue',
        };
    }

    /**
     * Retrieve the shape of the case
     *
     * @return string
     */
    public function shape(): string
    {
        return match ($this) {
            static::one => 'triangle',
            static::two => 'square',
            static::three => 'circle',
        };
    }

    /**
     * Retrieve whether the case is odd
     *
     * @return bool
     */
    public function isOdd(): bool
    {
        return match ($this) {
            static::one => true,
            static::two => false,
            static::three => true,
        };
    }
}
