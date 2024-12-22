<?php

declare(strict_types=1);

namespace Cerbero\Enum\Concerns;

use Cerbero\Enum\Enums;

/**
 * The trait to handle the magic methods of an enum.
 */
trait IsMagic
{
    /**
     * Handle the call to an inaccessible enum method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        return Enums::handleStaticCall(self::class, $name, $arguments);
    }

    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return Enums::handleCall($this, $name, $arguments);
    }

    /**
     * Handle the invocation of a case.
     */
    public function __invoke(mixed ...$arguments): mixed
    {
        return Enums::handleInvoke($this, ...$arguments);
    }
}
