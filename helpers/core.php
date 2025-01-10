<?php

declare(strict_types=1);

namespace Cerbero\Enum;

use Generator;

/**
 * Yield the content of the given path line by line.
 *
 * @return Generator<int, string>
 */
function yieldLines(string $path): Generator
{
    $stream = fopen($path, 'rb');

    try {
        while (($line = fgets($stream, 1024)) !== false) {
            yield $line;
        }
    } finally {
        is_resource($stream) && fclose($stream);
    }
}

/**
 * Retrieve the PSR-4 map of the composer file.
 *
 * @return array<string, string>
 */
function psr4(): array
{
    if (! is_file($path = Enums::basePath('composer.json'))) {
        return [];
    }

    $composer = (array) json_decode((string) file_get_contents($path), true);

    /** @var array<string, string> */
    return $composer['autoload']['psr-4'] ?? [];
}

/**
 * Retrieve the traits used by the given target recursively.
 *
 * @return array<class-string, class-string>
 */
function traitsUsedBy(string $target): array
{
    $traits = class_uses($target) ?: [];

    foreach ($traits as $trait) {
        $traits += traitsUsedBy($trait);
    }

    return $traits;
}

/**
 * Retrieve the given value in snake case.
 */
function snake(string $value, string $delimiter = '_'): string
{
    $value = preg_replace('/\s+/u', '', ucwords($value));

    return strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
}

/**
 * Retrieve the given value in camel case.
 */
function camel(string $value): string
{
    $words = explode(' ', str_replace(['-', '_'], ' ', $value));
    $studly = array_map('ucfirst', $words);

    return lcfirst(implode($studly));
}

/**
 * Parse the given raw string containing the name and value of a case.
 *
 * @return array<string, string|int>
 */
function parseCaseValue(string $raw): array
{
    [$rawName, $rawValue] = explode('=', $raw, limit: 2);
    $trimmed = trim($rawValue);
    $value = is_numeric($trimmed) ? (int) $trimmed : $trimmed;

    return [trim($rawName) => $value];
}

/**
 * Retrieve the backing type depending on the given value.
 */
function backingType(mixed $value): ?string
{
    return match (true) {
        is_int($value) => 'int',
        is_string($value) => str_contains($value, '<<') ? 'int' : 'string',
        default => null,
    };
}

/**
 * Retrieve the common type among the given types.
 */
function commonType(string ...$types): string
{
    $null = '';
    $types = array_unique($types);

    if (($index = array_search('null', $types)) !== false) {
        $null = '?';

        unset($types[$index]);
    }

    if (count($types) == 1) {
        return $null . reset($types);
    }

    return implode('|', $types) . ($null ? '|null' : '');
}

/**
 * Retrieve only the name of the given namespace.
 */
function className(string $namespace): string
{
    return basename(strtr($namespace, '\\', '/'));
}

/**
 * Split the given FQCN into namespace and name.
 *
 * @param class-string $namespace
 * @return list<string>
 */
function splitNamespace(string $namespace): array
{
    $segments = explode('\\', $namespace);
    $name = (string) array_pop($segments);

    return [implode('\\', $segments), $name];
}

/**
 * Retrieve the absolute path of the given namespace.
 *
 * @param class-string $namespace
 */
function namespaceToPath(string $namespace): string
{
    $path = Enums::basePath($namespace) . '.php';

    foreach (psr4() as $root => $relative) {
        if (str_starts_with($namespace, $root)) {
            $relative = path($relative) . DIRECTORY_SEPARATOR;

            return strtr($path, [$root => $relative]);
        }
    }

    return $path;
}

/**
 * Retrieve the normalized path.
 */
function path(string $path): string
{
    $segments = [];
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $path = rtrim($path, DIRECTORY_SEPARATOR);
    $head = str_starts_with($path, DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : '';

    foreach (explode(DIRECTORY_SEPARATOR, $path) as $segment) {
        if ($segment === '..') {
            array_pop($segments);
        } elseif ($segment !== '' && $segment !== '.') {
            $segments[] = $segment;
        }
    }

    return $head . implode(DIRECTORY_SEPARATOR, $segments);
}
