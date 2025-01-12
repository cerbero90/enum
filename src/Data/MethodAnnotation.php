<?php

declare(strict_types=1);

namespace Cerbero\Enum\Data;

use Stringable;
use UnitEnum;

/**
 * The method annotation.
 */
class MethodAnnotation implements Stringable
{
    /**
     * Whether the method is static.
     */
    public readonly bool $isStatic;

    /**
     * Retrieve the method annotation for the given case.
     */
    public static function forCase(UnitEnum $case): static
    {
        $returnType = is_int($case->value ?? null) ? 'int' : 'string';

        return new static($case->name, "static {$returnType} {$case->name}()");
    }

    /**
     * Retrieve the method annotation for an instance method.
     */
    public static function instance(string $name, string $returnType): static
    {
        return new static($name, "{$returnType} {$name}()");
    }

    /**
     * Instantiate the class.
     *
     * @param list<class-string> $namespaces
     */
    final public function __construct(
        public readonly string $name,
        public readonly string $annotation,
        public readonly array $namespaces = [],
    ) {
        $this->isStatic = str_starts_with($annotation, 'static');
    }

    /**
     * Retrieve the method annotation string.
     */
    public function __toString(): string
    {
        return "@method {$this->annotation}";
    }
}
