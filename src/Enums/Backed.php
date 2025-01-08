<?php

declare(strict_types=1);

namespace Cerbero\Enum\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\Enum\Concerns\Enumerates;
use Generator;

use function Cerbero\Enum\camel;
use function Cerbero\Enum\parseCaseValue;
use function Cerbero\Enum\snake;

/**
 * The backed value when generating new enums.
 */
enum Backed
{
    use Enumerates;

    #[Meta(label: 'The enum is pure, no values needed')]
    case pure;

    #[Meta(label: 'Custom values to assign manually')]
    case custom;

    #[Meta(label: 'The name in snake case (case_one)')]
    case snake;

    #[Meta(label: 'The name in camel case (caseOne)')]
    case camel;

    #[Meta(label: 'The name in kebab case (case-one)')]
    case kebab;

    #[Meta(label: 'The name in upper case (CASEONE)')]
    case upper;

    #[Meta(label: 'The name in lower case (caseone)')]
    case lower;

    #[Meta(label: 'Integer starting from 0 (0, 1, 2...)')]
    case int0;

    #[Meta(label: 'Integer starting from 1 (1, 2, 3...)')]
    case int1;

    #[Meta(label: 'Bitwise flag (1, 2, 4...)')]
    case bitwise;

    /**
     * Yield the case-value pairs.
     *
     * @return Generator<int, array<string, string|int|null>>
     */
    public function yieldPairs(): Generator
    {
        $i = 0;

        $callback = match ($this) {
            self::pure => fn(string $name) => [$name => null],
            self::custom => parseCaseValue(...),
            self::snake => fn(string $name) => [$name => snake($name)],
            self::camel => fn(string $name) => [$name => camel($name)],
            self::kebab => fn(string $name) => [$name => snake($name, '-')],
            self::upper => fn(string $name) => [$name => strtoupper($name)],
            self::lower => fn(string $name) => [$name => strtolower($name)],
            self::int0 => fn(string $name, int $i) => [$name => $i],
            self::int1 => fn(string $name, int $i) => [$name => $i + 1],
            self::bitwise => fn(string $name, int $i) => [$name => "1 << {$i}"],
        };

        /** @phpstan-ignore while.alwaysTrue */
        while (true) {
            /** @phpstan-ignore-next-line */
            yield $callback(yield, $i++);
        }
    }
}
