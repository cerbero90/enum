<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use ArrayIterator;
use Cerbero\Enum\Data\MethodAnnotation;
use IteratorAggregate;
use Traversable;
use UnitEnum;

use function Cerbero\Enum\commonType;

/**
 * The method annotations collector.
 *
 * @implements IteratorAggregate<string, MethodAnnotation>
 */
class MethodAnnotations implements IteratorAggregate
{
    /**
     * The regular expression to extract method annotations already annotated on the enum.
     *
     * @var string
     */
    public const RE_METHOD = '~@method\s+((?:static)?\s*[^\s]+\s+([^\(]+).*)~';

    /**
     * Instantiate the class.
     *
     * @param Inspector<UnitEnum> $inspector
     */
    public function __construct(
        protected Inspector $inspector,
        protected bool $includeExisting,
    ) {}

    /**
     * Retrieve the sorted, iterable method annotations.
     *
     * @return ArrayIterator<string, MethodAnnotation>
     */
    public function getIterator(): Traversable
    {
        $annotations = $this->all();

        uasort($annotations, function (MethodAnnotation $a, MethodAnnotation $b) {
            return [$b->isStatic, $a->name] <=> [$a->isStatic, $b->name];
        });

        return new ArrayIterator($annotations);
    }

    /**
     * Retrieve all the method annotations.
     *
     * @return array<string, MethodAnnotation>
     */
    public function all(): array
    {
        return [
            ...$this->forCaseNames(),
            ...$this->forMetaAttributes(),
            ...$this->includeExisting ? $this->existing() : [],
        ];
    }

    /**
     * Retrieve the method annotations for the case names.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forCaseNames(): array
    {
        $annotations = [];

        foreach ($this->inspector->cases() as $case) {
            $annotations[$case->name] = MethodAnnotation::forCase($case);
        }

        return $annotations;
    }

    /**
     * Retrieve the method annotations for the meta attributes.
     *
     * @return array<string, MethodAnnotation>
     */
    public function forMetaAttributes(): array
    {
        $annotations = [];
        $cases = $this->inspector->cases();

        foreach ($this->inspector->metaAttributeNames() as $meta) {
            $types = array_map(fn(UnitEnum $case) => get_debug_type($case->resolveMetaAttribute($meta)), $cases);

            $annotations[$meta] = MethodAnnotation::instance($meta, commonType(...$types));
        }

        return $annotations;
    }

    /**
     * Retrieve the method annotations already annotated on the enum.
     *
     * @return array<string, MethodAnnotation>
     */
    public function existing(): array
    {
        $annotations = [];

        preg_match_all(static::RE_METHOD, $this->inspector->docBlock(), $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $annotations[$match[2]] = new MethodAnnotation($match[2], $match[1]);
        }

        return $annotations;
    }
}
