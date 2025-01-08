<?php

declare(strict_types=1);

namespace App\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

#[Meta(next: null, isEven: false, alias: null)]
enum Enum1: int
{
    use Enumerates;

    #[Meta(next: 2, float: 1.0)]
    case One = 1;

    #[Meta(next: 3, isEven: true, float: 2.0)]
    case Two = 2;

    #[Meta(float: 3.0, alias: 'III')]
    case Three = 3;
}
