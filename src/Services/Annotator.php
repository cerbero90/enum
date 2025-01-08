<?php

declare(strict_types=1);

namespace Cerbero\Enum\Services;

use Cerbero\Enum\Data\MethodAnnotation;
use InvalidArgumentException;

use function Cerbero\Enum\className;

/**
 * The enums annotator.
 *
 * @template TEnum of \UnitEnum
 */
class Annotator
{
    /**
     * The regular expression to extract the use statements.
     *
     * @var string
     */
    public const RE_USE_STATEMENTS = '~^use(?:[\s\S]+(?=^use))?.+~im';

    /**
     * The regular expression to extract the enum declaring line with potential attributes.
     *
     * @var string
     */
    public const RE_ENUM = '~(^(?:#\[[\s\S]+)?^enum.+)~im';

    /**
     * The regular expression to extract the method annotations.
     *
     * @var string
     */
    public const RE_METHOD_ANNOTATIONS = '~^ \* @method(?:[\s\S]+(?=@method))?.+~im';

    /**
     * The enum inspector.
     *
     * @var Inspector<TEnum>
     */
    protected readonly Inspector $inspector;

    /**
     * Instantiate the class.
     *
     * @param class-string<TEnum> $enum
     * @throws InvalidArgumentException
     */
    public function __construct(protected readonly string $enum)
    {
        $this->inspector = new Inspector($enum);
    }

    /**
     * Annotate the given enum.
     */
    public function annotate(bool $overwrite = false): bool
    {
        if (empty($annotations = $this->inspector->methodAnnotations(! $overwrite))) {
            return true;
        }

        $docBlock = $this->inspector->docBlock();
        $filename = $this->inspector->filename();
        $oldContent = (string) file_get_contents($filename);
        $methodAnnotations = $this->formatMethodAnnotations($annotations);
        $useStatements = $this->formatUseStatements($this->inspector->useStatements(! $overwrite));
        $newContent = (string) preg_replace(static::RE_USE_STATEMENTS, $useStatements, $oldContent, 1);

        $newContent = match (true) {
            empty($docBlock) => $this->addDocBlock($methodAnnotations, $newContent),
            str_contains($docBlock, '@method') => $this->replaceAnnotations($methodAnnotations, $newContent),
            default => $this->addAnnotations($methodAnnotations, $newContent, $docBlock),
        };

        return file_put_contents($filename, $newContent) !== false;
    }

    /**
     * Retrieve the formatted method annotations.
     *
     * @param array<string, MethodAnnotation> $annotations
     */
    protected function formatMethodAnnotations(array $annotations): string
    {
        $mapped = array_map(fn(MethodAnnotation $annotation) => " * {$annotation}", $annotations);

        return implode(PHP_EOL, $mapped);
    }

    /**
     * Retrieve the formatted use statements.
     *
     * @param array<string, class-string> $statements
     */
    protected function formatUseStatements(array $statements): string
    {
        array_walk($statements, function (string &$namespace, string $alias) {
            $namespace = "use {$namespace}" . (className($namespace) == $alias ? ';' : " as {$alias};");
        });

        return implode(PHP_EOL, $statements);
    }

    /**
     * Add a docBlock with the given method annotations.
     */
    protected function addDocBlock(string $methodAnnotations, string $content): string
    {
        $replacement = implode(PHP_EOL, ['/**', $methodAnnotations, ' */', '$1']);

        return (string) preg_replace(static::RE_ENUM, $replacement, $content, 1);
    }

    /**
     * Replace existing method annotations with the given method annotations.
     */
    protected function replaceAnnotations(string $methodAnnotations, string $content): string
    {
        return (string) preg_replace(static::RE_METHOD_ANNOTATIONS, $methodAnnotations, $content, 1);
    }

    /**
     * Add the given method annotations to the provided docBlock.
     */
    protected function addAnnotations(string $methodAnnotations, string $content, string $docBlock): string
    {
        $newDocBlock = str_replace(' */', implode(PHP_EOL, [' *', $methodAnnotations, ' */']), $docBlock);

        return str_replace($docBlock, $newDocBlock, $content);
    }
}
