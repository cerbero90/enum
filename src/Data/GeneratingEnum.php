<?php

declare(strict_types=1);

namespace Cerbero\Enum\Data;

use function Cerbero\Enum\backingType;
use function Cerbero\Enum\namespaceToPath;
use function Cerbero\Enum\splitNamespace;

/**
 * The enum being generated.
 */
class GeneratingEnum
{
    /**
     * The namespace of the enum.
     */
    public readonly string $namespace;

    /**
     * The name of the enum.
     */
    public readonly string $name;

    /**
     * The absolute path of the enum.
     */
    public readonly string $path;

    /**
     * Whether the enum exists.
     */
    public readonly bool $exists;

    /**
     * The backing type, if backed.
     */
    public readonly ?string $backingType;

    /**
     * Instantiate the class.
     *
     * @param class-string<\UnitEnum> $fullNamespace
     * @param array<string, string|int|null> $cases
     */
    public function __construct(public readonly string $fullNamespace, public readonly array $cases)
    {
        [$this->namespace, $this->name] = splitNamespace($fullNamespace);

        $this->path = namespaceToPath($fullNamespace);

        $this->exists = file_exists($this->path);

        $this->backingType = backingType(reset($cases));
    }
}
