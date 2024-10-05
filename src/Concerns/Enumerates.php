<?php

namespace Cerbero\Enum\Concerns;

/**
 * The trait to supercharge the functionalities of an enum.
 */
trait Enumerates
{
    use CollectsCases;
    use Compares;
    use Hydrates;
    use IsMagic;
    use SelfAware;
}
