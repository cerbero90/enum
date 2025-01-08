<?php

declare(strict_types=1);

namespace Cerbero\Enum;

use Closure;
use Generator;
use GlobIterator;
use UnitEnum;

/**
 * The enums manager.
 */
class Enums
{
    /**
     * The application base path.
     *
     * @var string
     */
    protected static ?string $basePath = null;

    /**
     * The glob paths to find enums in.
     *
     * @var string[]
     */
    protected static array $paths = [];

    /**
     * The logic to run when an inaccessible enum method is called.
     *
     * @var ?Closure(class-string<UnitEnum> $enum, string $name, array<array-key, mixed> $arguments): mixed
     */
    protected static ?Closure $onStaticCall = null;

    /**
     * The logic to run when an inaccessible case method is called.
     *
     * @var ?Closure(UnitEnum $case, string $name, array<array-key, mixed> $arguments): mixed
     */
    protected static ?Closure $onCall = null;

    /**
     * The logic to run when a case is invoked.
     *
     * @var ?Closure(UnitEnum $case, mixed ...$arguments): mixed
     */
    protected static ?Closure $onInvoke = null;

    /**
     * Set the application base path.
     */
    public static function setBasePath(string $path): void
    {
        static::$basePath = path($path);
    }

    /**
     * Retrieve the application base path, optionally appending the given path.
     */
    public static function basePath(?string $path = null): string
    {
        $basePath = static::$basePath ?: dirname(__DIR__, 4);

        return $path === null ? $basePath : $basePath . DIRECTORY_SEPARATOR . ltrim(path($path), '\/');
    }

    /**
     * Set the glob paths to find all the application enums.
     */
    public static function setPaths(string ...$paths): void
    {
        static::$paths = array_map(path(...), $paths);
    }

    /**
     * Retrieve the paths to find all the application enums.
     *
     * @return string[]
     */
    public static function paths(): array
    {
        return static::$paths;
    }

    /**
     * Yield the namespaces of all the application enums.
     *
     * @return Generator<int, class-string<UnitEnum>>
     */
    public static function namespaces(): Generator
    {
        $psr4 = psr4();

        foreach (static::paths() as $path) {
            $pattern = static::basePath($path) . DIRECTORY_SEPARATOR . '*.php';

            foreach (new GlobIterator($pattern) as $fileInfo) {
                /** @var \SplFileInfo $fileInfo */
                $enumPath = (string) $fileInfo->getRealPath();

                foreach ($psr4 as $root => $relative) {
                    $absolute = static::basePath($relative) . DIRECTORY_SEPARATOR;

                    if (str_starts_with($enumPath, $absolute)) {
                        $enum = strtr($enumPath, [$absolute => $root, '/' => '\\', '.php' => '']);

                        if (enum_exists($enum)) {
                            yield $enum;
                        }
                    }
                }
            }
        }
    }

    /**
     * Set the logic to run when an inaccessible enum method is called.
     *
     * @param callable(class-string<UnitEnum> $enum, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onStaticCall(callable $callback): void
    {
        static::$onStaticCall = $callback(...);
    }

    /**
     * Set the logic to run when an inaccessible case method is called.
     *
     * @param callable(UnitEnum $case, string $name, array<array-key, mixed> $arguments): mixed $callback
     */
    public static function onCall(callable $callback): void
    {
        static::$onCall = $callback(...);
    }

    /**
     * Set the logic to run when a case is invoked.
     *
     * @param callable(UnitEnum $case, mixed ...$arguments): mixed $callback
     */
    public static function onInvoke(callable $callback): void
    {
        static::$onInvoke = $callback(...);
    }

    /**
     * Handle the call to an inaccessible enum method.
     *
     * @param class-string<UnitEnum> $enum
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
    public static function handleCall(UnitEnum $case, string $name, array $arguments): mixed
    {
        return static::$onCall ? (static::$onCall)($case, $name, $arguments) : $case->resolveMetaAttribute($name);
    }

    /**
     * Handle the invocation of a case.
     */
    public static function handleInvoke(UnitEnum $case, mixed ...$arguments): mixed
    {
        return static::$onInvoke ? (static::$onInvoke)($case, ...$arguments) : $case->value();
    }
}
