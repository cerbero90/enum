<?php

namespace Cerbero\Enum;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

/**
 * The pure enum to test.
 */
#[Meta(color: 'green', shape: 'square')]
enum PureEnum
{
    use Enumerates;

    #[Meta(color: 'red', shape: 'triangle')]
    case one;

    case two;

    #[Meta(color: 'blue', shape: 'circle')]
    case three;

    /**
     * Retrieve whether the case is odd
     *
     * @return bool
     */
    public function isOdd(): bool
    {
        return $this->name != 'two';
    }
}
