<?php

declare(strict_types=1);

namespace Domain\Payouts\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

/**
 * The enum 4.
 *
 * Secondary description.
 */
#[Meta(next: null, isEven: false, alias: null)]
enum Enum4: int
{
    use Enumerates;

    #[Meta(next: 2, float: 1.0)]
    case One = 1 << 0;

    #[Meta(next: 3.0, isEven: true, float: 2.0)]
    case Two = 1 << 1;

    #[Meta(next: null, float: 3.0, alias: 'III')]
    case Three = 1 << 2;
}
