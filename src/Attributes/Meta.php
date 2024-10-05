<?php

declare(strict_types=1);

namespace Cerbero\Enum\Attributes;

use Attribute;
use InvalidArgumentException;

/**
 * The attribute to declare the meta of an enum case.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE)]
class Meta
{
    /**
     * The declared meta and related values.
     *
     * @var array<string, mixed>
     */
    protected array $all;

    /**
     * Instantiate the class.
     */
    public function __construct(mixed ...$meta)
    {
        foreach ($meta as $key => $value) {
            if (! is_string($key)) {
                throw new InvalidArgumentException('The name of meta must be a string');
            }

            $this->all[$key] = $value;
        }
    }

    /**
     * Retrieve the meta names.
     *
     * @return string[]
     */
    public function names(): array
    {
        return array_keys($this->all);
    }

    /**
     * Determine whether the given meta exists.
     */
    public function has(string $meta): bool
    {
        return array_key_exists($meta, $this->all);
    }

    /**
     * Retrieve the value for the given meta.
     */
    public function get(string $meta): mixed
    {
        return $this->all[$meta] ?? null;
    }
}
