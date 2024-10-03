<?php

declare(strict_types=1);

namespace Cerbero\Enum;

use Closure;
use Error;

/**
 * The global behavior for all enums.
 */
class Enums
{
    /**
     * The logic to run when an inaccessible enum method is called.
     *
     * @var ?Closure(class-string $enum, string $name, array<array-key, mixed> $arguments): mixed
     */
    protected static ?Closure $onStaticCall = null;

    /**
     * The logic to run when an inaccessible case method is called.
     *
     * @var ?Closure(object $case, string $name, array<array-key, mixed> $arguments): mixed
     */
    protected static ?Closure $onCall = null;

    /**
     * The logic to run when a case is invoked.
     *
     * @var ?Closure(object $case, mixed ...$arguments): mixed
     */
    protected static ?Closure $onInvoke = null;

    /**
     * Set the logic to run when an inaccessible enum method is called.
     *
     * @param Closure(class-string $enum, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onStaticCall(Closure $callback): void
    {
        static::$onStaticCall = $callback;
    }

    /**
     * Set the logic to run when an inaccessible case method is called.
     *
     * @param Closure(object $case, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onCall(Closure $callback): void
    {
        static::$onCall = $callback;
    }

    /**
     * Set the logic to run when a case is invoked.
     *
     * @param Closure(object $case, mixed ...$arguments): mixed $callback
     */
    public static function onInvoke(Closure $callback): void
    {
        static::$onInvoke = $callback;
    }

    /**
     * Handle the call to an inaccessible enum method.
     *
     * @param class-string $enum
     * @param array<array-key, mixed> $arguments
     */
    public static function handleStaticCall(string $enum, string $name, array $arguments): mixed
    {
        return static::$onStaticCall
            ? (static::$onStaticCall)($enum, $name, $arguments)
            : $enum::fromName($name)->value();
    }

    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function handleCall(object $case, string $name, array $arguments): mixed
    {
        return static::$onCall
            ? (static::$onCall)($case, $name, $arguments) /** @phpstan-ignore-next-line property.notFound */
            : throw new Error(sprintf('Call to undefined method %s::%s->%s()', $case::class, $case->name, $name));
    }

    /**
     * Handle the invocation of a case.
     */
    public static function handleInvoke(object $case, mixed ...$arguments): mixed
    {
        /** @phpstan-ignore method.notFound */
        return static::$onInvoke ? (static::$onInvoke)($case, ...$arguments) : $case->value();
    }
}
