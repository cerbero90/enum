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
            self::one => 'red',
            self::two => 'green',
            self::three => 'blue',
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
            self::one => 'triangle',
            self::two => 'square',
            self::three => 'circle',
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
            self::one => true,
            self::two => false,
            self::three => true,
        };
    }
}
