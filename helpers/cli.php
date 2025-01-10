<?php

declare(strict_types=1);

namespace Cerbero\Enum;

use Closure;
use Throwable;

/**
 * Print out the given success message.
 */
function succeed(string $message): bool
{
    fwrite(STDOUT, "\e[38;2;38;220;38m{$message}\e[0m" . PHP_EOL);

    return true;
}

/**
 * Print out the given error message.
 */
function fail(string $message): bool
{
    fwrite(STDERR, "\e[38;2;220;38;38m{$message}\e[0m" . PHP_EOL);

    return false;
}

/**
 * Split the given argv into arguments and options.
 *
 * @param string[] $argv
 * @return list<string[]>
 */
function splitArgv(array $argv): array
{
    $arguments = $options = [];

    foreach (array_slice($argv, 2) as $item) {
        if (str_starts_with($item, '-')) {
            $options[] = $item;
        } else {
            $arguments[] = $item;
        }
    }

    return [$arguments, $options];
}

/**
 * Set enum paths from the given options.
 *
 * @param string[] $options
 */
function setPathsByOptions(array $options): void
{
    if ($basePath = option('base-path', $options)) {
        Enums::setBasePath($basePath);
    }

    if ($paths = option('paths', $options)) {
        Enums::setPaths(...explode(',', $paths));
    }
}

/**
 * Retrieve the value of the given option.
 *
 * @param string[] $options
 */
function option(string $name, array $options): ?string
{
    $prefix = "--{$name}=";

    foreach ($options as $option) {
        if (str_starts_with($option, $prefix)) {
            $segments = explode('=', $option, limit: 2);

            return $segments[1] === '' ? null : $segments[1];
        }
    }

    return null;
}

/**
 * Retrieve the normalized namespaces of the given enums.
 *
 * @param list<string> $enums
 * @return list<class-string<\UnitEnum>>
 */
function normalizeEnums(array $enums): array
{
    $namespaces = array_map(fn(string $enum) => strtr($enum, '/', '\\'), $enums);

    return array_unique(array_filter($namespaces, 'enum_exists'));
}

/**
 * Print out the outcome of the given enum operation.
 *
 * @param class-string<\UnitEnum> $namespace
 * @param Closure(): bool $callback
 */
function enumOutcome(string $enum, Closure $callback): bool
{
    $error = null;

    try {
        $succeeded = $callback();
    } catch (Throwable $e) {
        $succeeded = false;
        $error = "\e[38;2;220;38;38m{$e?->getMessage()}\e[0m";
    }

    if ($succeeded) {
        fwrite(STDOUT, "\e[48;2;163;230;53m\e[38;2;63;98;18m\e[1m DONE \e[0m {$enum}" . PHP_EOL);
    } else {
        fwrite(STDERR, "\e[48;2;248;113;113m\e[38;2;153;27;27m\e[1m FAIL \e[0m {$enum} {$error}" . PHP_EOL);
    }

    return $succeeded;
}

/**
 * Annotate the given enum within a new process.
 *
 * @param class-string<\UnitEnum> $enum
 */
function runAnnotate(string $enum, bool $force = false): bool
{
    // Once an enum is loaded, PHP accesses it from the memory and not from the disk.
    // Since we are writing on the disk, the enum in memory might get out of sync.
    // To ensure that the annotations reflect the current content of such enum,
    // we spin a new process to load in memory the latest state of the enum.
    // ob_start();

    $succeeded = cli("annotate \"{$enum}\"" . ($force ? ' --force' : ''));

    // ob_end_clean();

    return $succeeded;
}

/**
 * Run the enum CLI in a new process.
 */
function cli(string $command, ?int &$status = null): bool
{
    $cmd = vsprintf('"%s" "%s" %s --base-path="%s" --paths="%s" 2>&1', [
        PHP_BINARY,
        path(__DIR__ . '/../bin/enum'),
        $command,
        Enums::basePath(),
        implode(',', Enums::paths()),
    ]);

    return passthru($cmd, $status) === null;
}
