<?php

namespace Cerbero\Enum;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

/**
 * The backed enum to test.
 */
#[Meta(color: 'green', shape: 'square')]
enum BackedEnum: int
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case one = 1;

    case two = 2;

    #[Meta(color: 'blue', shape: 'circle')]
    case three = 3;

    /**
     * Retrieve whether the case is odd
     *
     * @return bool
     */
    public function isOdd(): bool
    {
        return $this->value % 2 != 0;
    }
}
