<?php

declare(strict_types=1);

namespace Cerbero\Enum;

use Closure;

/**
 * The enums manager.
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
     * @param callable(class-string $enum, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onStaticCall(callable $callback): void
    {
        static::$onStaticCall = $callback(...);
    }

    /**
     * Set the logic to run when an inaccessible case method is called.
     *
     * @param callable(object $case, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onCall(callable $callback): void
    {
        static::$onCall = $callback(...);
    }

    /**
     * Set the logic to run when a case is invoked.
     *
     * @param callable(object $case, mixed ...$arguments): mixed $callback
     */
    public static function onInvoke(callable $callback): void
    {
        static::$onInvoke = $callback(...);
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
            : $enum::fromName($name)->value(); /** @phpstan-ignore method.nonObject */
    }

    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function handleCall(object $case, string $name, array $arguments): mixed
    {
        /** @phpstan-ignore method.notFound */
        return static::$onCall ? (static::$onCall)($case, $name, $arguments) : $case->resolveMetaAttribute($name);
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
