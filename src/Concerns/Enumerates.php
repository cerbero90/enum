<?php

namespace Cerbero\Enum\Concerns;

/**
 * The trait to extend enum functionalities.
 *
 */
trait Enumerates
{
    use CollectsCases;
    use Compares;
    use Hydrates;
    use KeysAware;
    use SelfAware;
}
