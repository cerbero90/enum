<?php

namespace Cerbero\Enum\Concerns;

use Cerbero\Enum\CasesCollection;
use ValueError;

/**
 * The trait to hydrate an enum.
 */
trait Hydrates
{
    /**
     * Retrieve the case hydrated from the given name or fail.
     * This method can be called by pure enums only.
     *
     * @throws ValueError
     */
    public static function from(string $name): static
    {
        return static::fromName($name);
    }

    /**
     * Retrieve the case hydrated from the given name or fail.
     *
     * @throws ValueError
     */
    public static function fromName(string $name): static
    {
        if ($case = static::tryFromName($name)) {
            return $case;
        }

        throw new ValueError(sprintf('"%s" is not a valid name for enum "%s"', $name, static::class));
    }

    /**
     * Retrieve the case hydrated from the given name or NULL.
     */
    public static function tryFromName(string $name): ?static
    {
        foreach (static::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Retrieve the case hydrated from the given name or NULL.
     * This method can be called by pure enums only.
     */
    public static function tryFrom(string $name): ?static
    {
        return static::tryFromName($name);
    }

    /**
     * Retrieve all the cases hydrated from the given key or fail.
     *
     * @param (callable(self): mixed)|string $key
     * @return CasesCollection<array-key, self>
     * @throws ValueError
     */
    public static function fromKey(callable|string $key, mixed $value): CasesCollection
    {
        if ($cases = static::tryFromKey($key, $value)) {
            return $cases;
        }

        $target = is_callable($key) ? 'given callable key' : "key \"{$key}\"";

        throw new ValueError(sprintf('Invalid value for the %s for enum "%s"', $target, static::class));
    }

    /**
     * Retrieve all the cases hydrated from the given key or NULL.
     *
     * @param (callable(self): mixed)|string $key
     * @return ?CasesCollection<array-key, self>
     */
    public static function tryFromKey(callable|string $key, mixed $value): ?CasesCollection
    {
        $cases = [];

        foreach (static::cases() as $case) {
            if ($case->get($key) === $value) {
                $cases[] = $case;
            }
        }

        return $cases ? new CasesCollection($cases) : null;
    }
}
