<?php

namespace Cerbero\Enum\Concerns;

use BackedEnum;
use ReflectionClass;
use ReflectionMethod;
use Throwable;
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
        return is_subclass_of(self::class, BackedEnum::class);
    }

    /**
     * Retrieve all the keys of the enum.
     *
     * @return string[]
     */
    public static function keys(): array
    {
        $enum = new ReflectionClass(self::class);
        $keys = self::isPure() ? ['name'] : ['name', 'value'];

        foreach ($enum->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (! $method->isStatic() && $method->getFileName() == $enum->getFileName()) {
                $keys[] = $method->getShortName();
            }
        }

        return $keys;
    }

    /**
     * Retrieve the given key of this case.
     *
     * @template TGetValue
     *
     * @param (callable(self): TGetValue)|string $key
     * @return TGetValue
     * @throws ValueError
     */
    public function get(callable|string $key): mixed
    {
        try {
            return is_callable($key) ? $key($this) : ($this->$key ?? $this->$key());
        } catch (Throwable) {
            $target = is_callable($key) ? 'The given callable' : "\"{$key}\"";

            throw new ValueError(sprintf('%s is not a valid key for enum "%s"', $target, self::class));
        }
    }
}
