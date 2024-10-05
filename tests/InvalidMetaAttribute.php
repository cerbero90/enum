<?php

namespace Cerbero\Enum;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;

/**
 * The enum to test invalid meta attributes.
 */
#[Meta('ciao')]
enum InvalidMetaAttribute
{
    use Enumerates;

    case one;
    case two;
    case three;
}
