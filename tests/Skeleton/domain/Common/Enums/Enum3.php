<?php

declare(strict_types=1);

namespace Domain\Common\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

/**
 * The enum 3.
 *
 * @method static int count()
 */
#[Meta(next: null, isEven: false, alias: null)]
enum Enum3: string
{
    use Enumerates;

    #[Meta(next: 'next is 2', float: 1.0)]
    case One = 'number 1';

    #[Meta(next: 'next is 3', isEven: true, float: 2.0)]
    case Two = 'number 2';

    #[Meta(float: 3.0, alias: 'III')]
    case Three = 'number 3';
}
