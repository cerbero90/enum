<?php

declare(strict_types=1);

namespace Cerbero\Enum\Concerns;

use BackedEnum;
use Cerbero\Enum\Attributes\Meta;
use ReflectionAttribute;
use ReflectionEnum;
use ReflectionEnumUnitCase;
use ReflectionMethod;
use ValueError;

/**
 * The trait to make an enum self-aware.
 */
trait SelfAware
{
    /**
     * Determine whether the enum is pure.
     */
    public static function isPure(): bool
    {
        return !self::isBacked();
    }

    /**
     * Determine whether the enum is backed.
     */
    public static function isBacked(): bool
    {
        /** @phpstan-ignore function.impossibleType */
        return is_subclass_of(self::class, BackedEnum::class);
    }

    /**
     * Determine whether the enum is backed by integer.
     */
    public static function isBackedByInteger(): bool
    {
        return (string) (new ReflectionEnum(self::class))->getBackingType() === 'int';
    }

    /**
     * Determine whether the enum is backed by string.
     */
    public static function isBackedByString(): bool
    {
        return (string) (new ReflectionEnum(self::class))->getBackingType() === 'string';
    }

    /**
     * Retrieve all the meta names of the enum.
     *
     * @return list<string>
     */
    public static function metaNames(): array
    {
        $meta = self::metaAttributeNames();
        $enum = new ReflectionEnum(self::class);

        foreach ($enum->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (! $method->isStatic() && $method->getFileName() == $enum->getFileName()) {
                $meta[] = $method->getShortName();
            }
        }

        return array_values(array_unique($meta));
    }

    /**
     * Retrieve all the meta attribute names of the enum.
     *
     * @return list<string>
     */
    public static function metaAttributeNames(): array
    {
        $meta = [];
        $enum = new ReflectionEnum(self::class);

        foreach ($enum->getAttributes(Meta::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            array_push($meta, ...$attribute->newInstance()->names());
        }

        foreach ($enum->getCases() as $case) {
            foreach ($case->getAttributes(Meta::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
                array_push($meta, ...$attribute->newInstance()->names());
            }
        }

        return array_values(array_unique($meta));
    }

    /**
     * Retrieve the given item of this case.
     *
     * @template TItemValue
     *
     * @param (callable(self): TItemValue)|string $item
     * @return TItemValue
     * @throws ValueError
     */
    public function resolveItem(callable|string $item): mixed
    {
        return match (true) {
            is_callable($item) => $item($this),
            property_exists($this, $item) => $this->$item,
            default => $this->resolveMeta($item),
        };
    }

    /**
     * Retrieve the given meta of this case.
     *
     * @throws ValueError
     */
    public function resolveMeta(string $meta): mixed
    {
        $enum = new ReflectionEnum($this);
        $enumFileName = $enum->getFileName();

        foreach ($enum->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (! $method->isStatic() && $method->getFileName() == $enumFileName && $method->getShortName() == $meta) {
                return $this->$meta();
            }
        }

        return $this->resolveMetaAttribute($meta);
    }

    /**
     * Retrieve the given meta from the attributes.
     *
     * @throws ValueError
     */
    public function resolveMetaAttribute(string $meta): mixed
    {
        $case = new ReflectionEnumUnitCase($this, $this->name);

        foreach ($case->getAttributes(Meta::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            if (($metadata = $attribute->newInstance())->has($meta)) {
                return $metadata->get($meta);
            }
        }

        foreach ($case->getEnum()->getAttributes(Meta::class, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            if (($metadata = $attribute->newInstance())->has($meta)) {
                return $metadata->get($meta);
            }
        }

        throw new ValueError(sprintf('The case %s::%s has no "%s" meta set', self::class, $this->name, $meta));
    }

    /**
     * Retrieve the value of a backed case or the name of a pure case.
     */
    public function value(): string|int
    {
        /** @var string|int @phpstan-ignore property.notFound */
        return $this->value ?? $this->name;
    }
}
