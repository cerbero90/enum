<?php

declare(strict_types=1);

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
    public static function from(int|string $name): static
    {
        return self::fromName($name);
    }

    /**
     * Retrieve the case hydrated from the given name or fail.
     *
     * @throws ValueError
     */
    public static function fromName(int|string $name): static
    {
        if ($case = self::tryFromName($name)) {
            return $case;
        }

        throw new ValueError(sprintf('"%s" is not a valid name for enum "%s"', $name, self::class));
    }

    /**
     * Retrieve the case hydrated from the given name or NULL.
     */
    public static function tryFromName(int|string $name): ?static
    {
        foreach (self::cases() as $case) {
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
    public static function tryFrom(int|string $name): ?static
    {
        return self::tryFromName($name);
    }

    /**
     * Retrieve all the cases hydrated from the given meta or fail.
     *
     * @return CasesCollection<self>
     * @throws ValueError
     */
    public static function fromMeta(string $meta, mixed $value = true): CasesCollection
    {
        if ($cases = self::tryFromMeta($meta, $value)) {
            return $cases;
        }

        throw new ValueError(sprintf('Invalid value for the meta "%s" for enum "%s"', $meta, self::class));
    }

    /**
     * Retrieve all the cases hydrated from the given meta or NULL.
     *
     * @return ?CasesCollection<self>
     */
    public static function tryFromMeta(string $meta, mixed $value = true): ?CasesCollection
    {
        $cases = [];

        foreach (self::cases() as $case) {
            $metaValue = $case->resolveMeta($meta);

            if ((is_callable($value) && $value($metaValue) === true) || $metaValue === $value) {
                $cases[] = $case;
            }
        }

        return $cases ? new CasesCollection($cases) : null;
    }
}
