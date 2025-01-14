<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use Cerbero\Enum\Concerns\Enumerates;
use Cerbero\Enum\Data\MethodAnnotation;
use InvalidArgumentException;
use ReflectionEnum;

use function Cerbero\Enum\traitsUsedBy;

/**
 * The enum inspector.
 *
 * @template TEnum of \UnitEnum
 */
class Inspector
{
    /**
     * The main trait to supercharge enums.
     */
    protected string $mainTrait = Enumerates::class;

    /**
     * The enum reflection.
     *
     * @var ReflectionEnum<TEnum>
     */
    protected ReflectionEnum $reflection;

    /**
     * The method annotations.
     *
     * @var array<string, MethodAnnotation>
     */
    protected array $methodAnnotations;

    /**
     * The use statements.
     *
     * @var array<string, class-string>
     */
    protected array $useStatements;

    /**
     * Instantiate the class.
     *
     * @param class-string<TEnum> $enum
     */
    public function __construct(protected string $enum)
    {
        $this->reflection = new ReflectionEnum($enum);

        $this->assertEnumUsesMainTrait();
    }

    /**
     * Assert that the enum uses the main trait.
     */
    protected function assertEnumUsesMainTrait(): void
    {
        if (! $this->uses($this->mainTrait)) {
            throw new InvalidArgumentException("The enum {$this->enum} must use the trait {$this->mainTrait}");
        }
    }

    /**
     * Retrieve the enum filename.
     */
    public function filename(): string
    {
        return (string) $this->reflection->getFileName();
    }

    /**
     * Retrieve the DocBlock of the enum.
     */
    public function docBlock(): string
    {
        return $this->reflection->getDocComment() ?: '';
    }

    /**
     * Retrieve the enum cases.
     *
     * @return list<TEnum>
     */
    public function cases(): array
    {
        /** @var list<TEnum> */
        return $this->enum::cases();
    }

    /**
     * Retrieve the meta attribute names of the enum.
     *
     * @return list<string>
     */
    public function metaAttributeNames(): array
    {
        /** @var list<string> */
        return $this->enum::metaAttributeNames();
    }

    /**
     * Determine whether the enum uses the given trait.
     */
    public function uses(string $trait): bool
    {
        return isset($this->traits()[$trait]);
    }

    /**
     * Retrieve all the enum traits.
     *
     * @return array<class-string, class-string>
     */
    public function traits(): array
    {
        $traits = [];

        foreach ($this->reflection->getTraitNames() as $trait) {
            $traits += [$trait => $trait, ...traitsUsedBy($trait)];
        }

        /** @var array<class-string, class-string> */
        return $traits;
    }

    /**
     * Retrieve the use statements.
     *
     * @return array<string, class-string>
     */
    public function useStatements(bool $includeExisting = true): array
    {
        return $this->useStatements ??= [...new UseStatements($this, $includeExisting)];
    }

    /**
     * Retrieve the method annotations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function methodAnnotations(bool $includeExisting = true): array
    {
        return $this->methodAnnotations ??= [...new MethodAnnotations($this, $includeExisting)];
    }
}
