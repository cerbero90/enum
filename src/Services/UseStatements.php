<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

use function Cerbero\Enum\className;
use function Cerbero\Enum\yieldLines;

/**
 * The use statements collector.
 *
 * @implements IteratorAggregate<string, class-string>
 */
class UseStatements implements IteratorAggregate
{
    /**
     * The regular expression to extract the use statements already present on the enum.
     *
     * @var string
     */
    public const RE_STATEMENT = '~^use\s+([^\s;]+)(?:\s+as\s+([^;]+))?~i';

    /**
     * Instantiate the class.
     *
     * @param Inspector<\UnitEnum> $inspector
     */
    public function __construct(
        protected Inspector $inspector,
        protected bool $includeExisting,
    ) {}

    /**
     * Retrieve the sorted, iterable use statements.
     *
     * @return ArrayIterator<string, class-string>
     */
    public function getIterator(): Traversable
    {
        $useStatements = $this->all();

        asort($useStatements);

        return new ArrayIterator($useStatements);
    }

    /**
     * Retrieve all the use statements.
     *
     * @return array<string, class-string>
     */
    public function all(): array
    {
        return $this->existing();
    }

    /**
     * Retrieve the use statements already present on the enum.
     *
     * @return array<string, class-string>
     */
    public function existing(): array
    {
        $useStatements = [];

        foreach (yieldLines($this->inspector->filename()) as $line) {
            if (strpos($line, 'enum') === 0) {
                break;
            }

            if (preg_match(static::RE_STATEMENT, $line, $matches)) {
                $useStatements[$matches[2] ?? className($matches[1])] = $matches[1];
            }
        }

        /** @var array<string, class-string> */
        return $useStatements;
    }
}
