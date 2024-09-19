<?php

namespace Cerbero\Enum;

use Cerbero\Enum\Concerns\Enumerates;

/**
 * The backed enum to test.
 *
 */
enum BackedEnum: int
{
    use Enumerates;

    case one = 1;
    case two = 2;
    case three = 3;

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
